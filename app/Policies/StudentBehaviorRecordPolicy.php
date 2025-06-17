<?php

namespace App\Policies;

use App\Models\StudentBehaviorRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentBehaviorRecordPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentBehaviorRecord Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher']; // Student/Parent ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງບັນທຶກໄດ້
        $createRoles = ['admin', 'school_admin', 'teacher']; // ຄູສາມາດບັນທຶກພຶດຕິກຳໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດແກ້ໄຂໄດ້
        $updateRoles = ['admin', 'school_admin']; // Owner ແກ້ໄຂຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ບັນທຶກພຶດຕິກຳທັງໝົດ (ຂອງນັກຮຽນຄົນໃດຄົນໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // Context check likely in controller
            ],
            // ໃຜສາມາດເບິ່ງບັນທຶກພຶດຕິກຳສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງ, ຜູ້ບັນທຶກເດີມ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent', 'isRecorder'],
            ],
            // ໃຜສາມາດສ້າງບັນທຶກພຶດຕິກຳໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['record_behavior'], // ຕົວຢ່າງ permission
                // ການກວດສອບວ່າ Teacher ກ່ຽວຂ້ອງກັບ Student ຫຼືບໍ່ ຄວນເຮັດກ່ອນເອີ້ນ Policy
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກພຶດຕິກຳໄດ້?
            'update' => [
                'roles' => $updateRoles, // Admin, School Admin
                'permissions' => ['manage_behavior_records'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ບັນທຶກເດີມອາດຈະແກ້ໄຂໄດ້ (ພາຍໃນເວລາທີ່ກຳນົດ)
                'exceptions' => ['isRecorder'],
            ],
            // ໃຜສາມາດລຶບບັນທຶກພຶດຕິກຳໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                'exceptions' => [],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Behavior record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentBehaviorRecord || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Behavior record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentBehaviorRecord || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດ StudentBehaviorRecord model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $student->student_id)->exists() ?? false;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ບັນທຶກ Behavior record ນີ້ ຫຼື ບໍ່.
     * (ໂດຍກວດສອບ teacher_id)
     */
    protected function isRecorder(User $user, Model $model): bool
    {
        if (!$model instanceof StudentBehaviorRecord || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }
        // ກວດສອບວ່າ teacher_id ຂອງ User ກົງກັບ teacher_id ໃນ record ຫຼືບໍ່
        // ສົມມຸດວ່າ User model ມີ relationship 'teacher' ແລະ Teacher model ມີ PK 'teacher_id'
        return !is_null($model->teacher_id) && $user->teacher?->teacher_id === $model->teacher_id;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
