<?php

namespace App\Policies;

use App\Models\User;

class SchoolStoreItemPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ SchoolStoreItem Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍການສິນຄ້າໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $viewListRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff', 'store_manager']; // ສົມມຸດວ່າມີ Role 'store_manager'
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ສິນຄ້າໄດ້
        $managementRoles = ['admin', 'school_admin', 'store_manager'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບສິນຄ້າໄດ້
        $deletionRoles = ['admin', 'school_admin']; // ຈຳກັດການລຶບ

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ສິນຄ້າທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດສິນຄ້າສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewListRoles, // ທຸກຄົນເບິ່ງລາຍລະອຽດໄດ້
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງ (ເພີ່ມ) ສິນຄ້າໃໝ່ເຂົ້າຮ້ານໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_store_items'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນສິນຄ້າໄດ້? (ເຊັ່ນ: ລາຄາ, ລາຍລະອຽດ)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_store_items'], // ຕົວຢ່າງ permission
                'exceptions' => [], // ການອັບເດດ stock ອາດຈະເຮັດຜ່ານການຂາຍ/ຮັບສິນຄ້າ
            ],
            // ໃຜສາມາດລຶບສິນຄ້າອອກຈາກລະບົບໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                // Foreign key constraint ໃນ StoreSales ຈະປ້ອງກັນການລຶບຖ້າມີການຂາຍແລ້ວ
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
     * Helper method: ກວດສອບວ່າຜູ້ໃຊ້ເປັນ Store Manager ຫຼື ບໍ່ (ຕົວຢ່າງ).
     * (ຫຼື ຈະເອົາໄປໄວ້ໃນ AppPolicy ຖ້າໃຊ້ຫຼາຍບ່ອນ)
     */
    protected function isStoreManager(User $user): bool
    {
        // ປັບຕາມການກວດສອບ Role ຕົວຈິງຂອງທ່ານ
        return $user->role && $user->role->role_name === 'store_manager';
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
