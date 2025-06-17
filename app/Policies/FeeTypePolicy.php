<?php

namespace App\Policies;

use App\Models\User;

class FeeTypePolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ FeeType Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ອາດຈະກວ້າງກວ່າ)
        $viewRoles = ['admin', 'school_admin', 'finance_staff', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້ (ຕາມແຜນ)
        $managementRoles = ['admin', 'finance_staff']; // ສົມມຸດ Role ການເງິນຊື່ 'finance_staff'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ປະເພດຄ່າທຳນຽມທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນປະເພດຄ່າທຳນຽມສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງປະເພດຄ່າທຳນຽມໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_fees'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນປະເພດຄ່າທຳນຽມໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_fees'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນປະເພດຄ່າທຳນຽມໄດ້? (Soft Delete)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນປະເພດຄ່າທຳນຽມທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນປະເພດຄ່າທຳນຽມອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
