<?php

namespace App\Policies;

use App\Models\ClassSubject;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GradePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Grade Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຄະແນນທັງໝົດໄດ້? (ຄວນ Filter ໃນ Controller/Resource)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຄະແນນສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
                // isTeacherOfGrade ອາດຈະກວດສອບຜ່ານ role 'teacher' ແລ້ວ ຫຼື ເພີ່ມໃນ exception ກໍ່ໄດ້
            ],
            // ໃຜສາມາດສ້າງຄະແນນໃໝ່ໄດ້?
            'create' => [
                // ກວດສອບ Role ກ່ອນ (Admin, School Admin, Teacher)
                // ການກວດສອບວ່າ Teacher ສາມາດໃຫ້ຄະແນນວິຊາ/ຫ້ອງນີ້ໄດ້ບໍ່ ຄວນເຮັດໃນ Controller/Request
                'roles' => $managementRoles,
                'permissions' => ['record_grades'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຄະແນນໄດ້?
            'update' => [
                'roles' => $managementRoles, // Admin, School Admin, Teacher ແກ້ໄຂໄດ້
                'permissions' => ['edit_grades'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ໃຫ້ຄູທີ່ສອນວິຊານັ້ນ ຫຼື ຜູ້ບັນທຶກເດີມແກ້ໄຂໄດ້
                'exceptions' => ['isTeacherOfGrade', 'isGrader'],
            ],
            // ໃຜສາມາດລຶບຄະແນນໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin ລຶບໄດ້
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
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Grade record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof Grade || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Grade record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof Grade || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model
        // ແລະ Guardian->students relationship ມີຢູ່
        return $user->guardian?->students()->where('students.student_id', $model->student_id)->exists() ?? false;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ສອນວິຊາ/ຫ້ອງ ຂອງ Grade record ນີ້ ຫຼື ບໍ່.
     */
    protected function isTeacherOfGrade(User $user, Model $model): bool
    {
        if (!$model instanceof Grade || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        $teacherId = $user->teacher?->getKey();
        if (!$teacherId) return false;

        // ກວດສອບໃນຕາຕະລາງ class_subjects ວ່າຄູຄົນນີ້ສອນວິຊານີ້ໃນຫ້ອງນີ້ບໍ່
        // ສົມມຸດ Model ClassSubject ມີຢູ່
        return ClassSubject::where('class_id', $model->class_id)
            ->where('subject_id', $model->subject_id)
            ->where('teacher_id', $teacherId)
            ->exists();
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ໃຫ້ຄະແນນ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGrader(User $user, Model $model): bool
    {
        if (!$model instanceof Grade) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ graded_by ຫຼືບໍ່
        return !is_null($model->graded_by) && $user->user_id === $model->graded_by;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
