<?php

namespace App\Policies;

use App\Models\ClassSubject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectPolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ ClassSubject Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການມອບໝາຍທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການມອບໝາຍສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຄູທີ່ຖືກມອບໝາຍ, ຄູປະຈຳຫ້ອງຂອງຫ້ອງນັ້ນ
                'exceptions' => ['isAssignedTeacher', 'isHomeroomTeacherOfAssignedClass'],
            ],
            // ໃຜສາມາດສ້າງການມອບໝາຍໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_class_subjects'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂການມອບໝາຍໄດ້? (ເຊັ່ນ: ປ່ຽນຄູ, ປ່ຽນຊົ່ວໂມງ, ປ່ຽນສະຖານະ)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_class_subjects'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຄູທີ່ຖືກມອບໝາຍອາດຈະແກ້ໄຂບາງຢ່າງໄດ້ (ເຊັ່ນ: status)
                'exceptions' => ['isAssignedTeacher'],
            ],
            // ໃຜສາມາດລຶບການມອບໝາຍໄດ້?
            'delete' => [
                'roles' => $managementRoles, // Admin, School Admin ລຶບໄດ້
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
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ຖືກມອບໝາຍໃນ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isAssignedTeacher(User $user, Model $model): bool
    {
        if (!$model instanceof ClassSubject || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }
        // ກວດສອບວ່າ teacher_id ຂອງ User ກົງກັບ teacher_id ຂອງ ClassSubject record ຫຼືບໍ່
        return !is_null($model->teacher_id) && $user->teacher?->teacher_id === $model->teacher_id;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູປະຈຳຫ້ອງຂອງຫ້ອງຮຽນທີ່ກ່ຽວຂ້ອງກັບ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isHomeroomTeacherOfAssignedClass(User $user, Model $model): bool
    {
        if (!$model instanceof ClassSubject || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ດຶງເອົາ Class ທີ່ກ່ຽວຂ້ອງຜ່ານ Relationship (ສົມມຸດວ່າມີ 'schoolClass' relationship ໃນ ClassSubject Model)
        $class = $model->schoolClass; // ຄວນຈະ eager load ຖ້າໃຊ້ໃນ list
        if (!$class) {
            return false; // ບໍ່ພົບຫ້ອງຮຽນທີ່ກ່ຽວຂ້ອງ
        }

        // ກວດສອບວ່າ teacher_id ຂອງ User ກົງກັບ homeroom_teacher_id ຂອງ Class ຫຼືບໍ່
        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        return !is_null($class->homeroom_teacher_id) && $user->teacher?->teacher_id === $class->homeroom_teacher_id;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
