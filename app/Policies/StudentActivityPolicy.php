<?php

namespace App\Policies;

use App\Models\StudentActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentActivityPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentActivity Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher']; // Student/Parent/Coordinator ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງ (ລົງທະບຽນ) ໄດ້
        $createRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent']; // ອະນຸຍາດໃຫ້ ນຮ/ຜປຄ ລົງທະບຽນເອງ/ໃຫ້ລູກ
        // ກຳນົດ Roles ທີ່ສາມາດແກ້ໄຂ (ເຊັ່ນ: ປ່ຽນ status, performance)
        $updateRoles = ['admin', 'school_admin']; // Coordinator ແກ້ໄຂຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດລຶບ (ເອົາອອກຈາກກິດຈະກຳ)
        $deletionRoles = ['admin', 'school_admin']; // Coordinator/Owner ລຶບຜ່ານ exception

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການເຂົ້າຮ່ວມທັງໝົດ (ຂອງກິດຈະກຳໃດໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // Context check likely in controller
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການເຂົ້າຮ່ວມສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ປະສານງານກິດຈະກຳ, ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isCoordinatorOfActivity', 'isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດສ້າງ (ລົງທະບຽນ) ການເຂົ້າຮ່ວມໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['register_activities'], // ຕົວຢ່າງ permission
                // ການກວດສອບວ່າ ນຮ/ຜປຄ ລົງທະບຽນໃຫ້ຖືກຄົນ ແມ່ນເຮັດໃນ Controller/Request
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນການເຂົ້າຮ່ວມໄດ້? (ເຊັ່ນ: status, performance)
            'update' => [
                'roles' => $updateRoles, // Admin, School Admin
                'permissions' => ['manage_student_activities'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ປະສານງານກິດຈະກຳ
                'exceptions' => ['isCoordinatorOfActivity'],
            ],
            // ໃຜສາມາດລຶບ (ເອົານັກຮຽນອອກ) ການເຂົ້າຮ່ວມໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ປະສານງານກິດຈະກຳ, ນັກຮຽນເຈົ້າຂອງ (ຖ້າອະນຸຍາດໃຫ້ອອກເອງ)
                'exceptions' => ['isCoordinatorOfActivity', 'isOwnerOfRecord'],
            ],
            // Restore/ForceDelete (ຕາຕະລາງນີ້ອາດຈະບໍ່ໃຊ້ Soft Deletes)
            'restore' => [
                'roles' => ['admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
            'forceDelete' => [
                'roles' => ['admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປະສານງານຂອງກິດຈະກຳທີ່ record ນີ້ສັງກັດ ຫຼື ບໍ່.
     */
    protected function isCoordinatorOfActivity(User $user, Model $model): bool
    {
        if (!$model instanceof StudentActivity) {
            return false;
        }
        // ສົມມຸດ StudentActivity model ມີ relationship 'activity'
        $activity = $model->activity; // ຄວນ eager load ຖ້າໃຊ້ໃນ list
        if (!$activity) return false;

        // ກວດສອບວ່າ user_id ກົງກັບ coordinator_id ຂອງ Activity ຫຼືບໍ່
        return !is_null($activity->coordinator_id) && $user->user_id === $activity->coordinator_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Activity record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentActivity || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Activity record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentActivity || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດ StudentActivity model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $student->student_id)->exists() ?? false;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
