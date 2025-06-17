<?php

namespace App\Policies;

use App\Models\StudentDiscount;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentDiscountPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentDiscount Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ອາດຈະກວ້າງ)
        $viewRoles = ['admin', 'school_admin', 'finance_staff', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'finance_staff']; // Admin ແລະ ການເງິນ
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'finance_staff']; // Admin ແລະ ການເງິນ ສາມາດລຶບການມອບໝາຍໄດ້

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການມອບໝາຍສ່ວນຫຼຸດທັງໝົດໄດ້? (ຄວນ Filter)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການມອບໝາຍສ່ວນຫຼຸດສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, Finance ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດສ້າງການມອບໝາຍສ່ວນຫຼຸດໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['assign_discounts'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂການມອບໝາຍສ່ວນຫຼຸດໄດ້? (ເຊັ່ນ: ປ່ຽນວັນທີ, ສະຖານະ)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['assign_discounts'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບການມອບໝາຍສ່ວນຫຼຸດໄດ້?
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Discount record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentDiscount || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Discount record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentDiscount || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $model->student_id)->exists() ?? false;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
