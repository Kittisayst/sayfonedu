<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SchedulePolicy extends AppPolicy
{
     /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Schedule Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent']; // ອະນຸຍາດໃຫ້ທຸກຄົນເບິ່ງຕາຕະລາງໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງຕາຕະລາງສອນທັງໝົດໄດ້? (ອາດຈະ filter ຕາມຫ້ອງ/ຄູ ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດຕາຕະລາງສອນສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles, // ອະນຸຍາດໃຫ້ທຸກຄົນເບິ່ງລາຍລະອຽດໄດ້
                'permissions' => [],
                'exceptions' => [], // ບໍ່ມີຂໍ້ຍົກເວັ້ນພິເສດສຳລັບການເບິ່ງ
            ],
            // ໃຜສາມາດສ້າງຕາຕະລາງສອນໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_schedules'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຕາຕະລາງສອນໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_schedules'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຄູທີ່ສອນໃນຊົ່ວໂມງນັ້ນ ອາດຈະແກ້ໄຂບາງຢ່າງໄດ້ (ເຊັ່ນ: ຫ້ອງ)
                'exceptions' => ['isAssignedTeacher'],
            ],
            // ໃຜສາມາດລຶບຕາຕະລາງສອນໄດ້?
            'delete' => [
                'roles' => $deletionRoles,
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
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ຖືກມອບໝາຍໃນ Schedule record ນີ້ ຫຼື ບໍ່.
     */
    protected function isAssignedTeacher(User $user, Model $model): bool
    {
        if (!$model instanceof Schedule || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }
        // ກວດສອບວ່າ teacher_id ຂອງ User ກົງກັບ teacher_id ຂອງ Schedule record ຫຼືບໍ່
        return !is_null($model->teacher_id) && $user->teacher?->teacher_id === $model->teacher_id;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູປະຈຳຫ້ອງຂອງຫ້ອງຮຽນທີ່ກ່ຽວຂ້ອງກັບ Schedule record ນີ້ ຫຼື ບໍ່.
     * (ອາດຈະບໍ່ຈຳເປັນສຳລັບ Schedule Policy ໂດຍກົງ ແຕ່ມີໄວ້ກໍ່ໄດ້)
     */
    protected function isHomeroomTeacherOfAssignedClass(User $user, Model $model): bool
    {
        if (!$model instanceof Schedule || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ດຶງເອົາ Class ທີ່ກ່ຽວຂ້ອງຜ່ານ Relationship (ສົມມຸດວ່າມີ 'schoolClass' relationship ໃນ Schedule Model)
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
