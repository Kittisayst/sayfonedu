<?php

namespace App\Policies;

use App\Models\StoreSale;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StoreSalePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StoreSale Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນການຂາຍໄດ້
        $viewRoles = ['admin', 'school_admin', 'finance_staff', 'store_manager']; // Seller/Buyer ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດບັນທຶກການຂາຍໄດ້
        $managementRoles = ['admin', 'school_admin', 'store_manager', 'finance_staff']; // ສົມມຸດວ່າມີ Role 'store_manager'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດທີ່ສຸດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການຂາຍທັງໝົດໄດ້? (ຄວນ Filter)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການຂາຍສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Role ຫຼັກເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຂາຍ ຫຼື ຜູ້ຊື້
                'exceptions' => ['isSeller', 'isBuyer'],
            ],
            // ໃຜສາມາດສ້າງ (ບັນທຶກ) ການຂາຍໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['record_store_sales'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນການຂາຍໄດ້? (ປົກກະຕິບໍ່ຄວນແກ້ໄຂ, ອາດຈະແກ້ໄຂໝາຍເຫດ?)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_store_sales'], // ຕົວຢ່າງ permission
                'exceptions' => [], // ອາດຈະໃຫ້ isSeller ແກ້ໄຂ note ໄດ້?
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນການຂາຍໄດ້? (ບໍ່ຄວນລຶບ)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ
                'permissions' => [],
                'exceptions' => [],
            ],
            // Restore/ForceDelete (ຕາຕະລາງນີ້ອາດຈະບໍ່ໃຊ້ Soft Deletes)
            'restore' => [
                'roles' => $deletionRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            'forceDelete' => [
                'roles' => $deletionRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຂາຍ/ບັນທຶກ Sale record ນີ້ ຫຼື ບໍ່.
     */
    protected function isSeller(User $user, Model $model): bool
    {
        if (!$model instanceof StoreSale) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ sold_by ຫຼືບໍ່
        return $user->user_id === $model->sold_by;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຊື້ໃນ Sale record ນີ້ ຫຼື ບໍ່.
     * (ຈັດການ Polymorphic relationship)
     */
    protected function isBuyer(User $user, Model $model): bool
    {
        if (!$model instanceof StoreSale || is_null($model->buyer_type) || is_null($model->buyer_id)) {
            return false;
        }

        switch ($model->buyer_type) {
            case 'student':
                // ສົມມຸດ User model ມີ relationship 'student'
                return $this->isStudent($user) && $user->student?->student_id === $model->buyer_id;
            case 'teacher':
                // ສົມມຸດ User model ມີ relationship 'teacher'
                return $this->isTeacher($user) && $user->teacher?->teacher_id === $model->buyer_id;
            case 'parent':
                // ສົມມຸດ User model ມີ relationship 'guardian' (ທີ່ return StudentParents model)
                // ແລະ StudentParents model ມີ PK 'parent_id'
                return $this->isParent($user) && $user->guardian?->parent_id === $model->buyer_id;
            case 'other':
            default:
                return false;
        }
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
