<?php

namespace App\Policies;

use App\Models\User;

class EthnicityPolicy extends AppPolicy
{
 /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Ethnicity Policy.
     * ອະນຸຍາດໃຫ້ຜູ້ໃຊ້ສ່ວນໃຫຍ່ເບິ່ງໄດ້, ແຕ່ຈຳກັດການຈັດການໃຫ້ Admin.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນຊົນເຜົ່າໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $viewRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff']; // ປັບຕາມຄວາມເໝາະສົມ
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້ (Admin ເທົ່ານັ້ນ)
        $adminOnly = [
            'roles' => ['admin'],
            'permissions' => ['manage_master_data'], // ຕົວຢ່າງ permission (optional)
            'exceptions' => [],
        ];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຊົນເຜົ່າທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນຊົນເຜົ່າສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງຊົນເຜົ່າໃໝ່ໄດ້?
            'create' => $adminOnly,
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຊົນເຜົ່າໄດ້?
            'update' => $adminOnly,
            // ໃຜສາມາດລຶບຂໍ້ມູນຊົນເຜົ່າໄດ້?
            'delete' => $adminOnly,
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນຊົນເຜົ່າທີ່ຖືກລຶບໄດ້?
            'restore' => $adminOnly, // ອາດຈະບໍ່ໃຊ້ Soft Deletes
            // ໃຜສາມາດລຶບຂໍ້ມູນຊົນເຜົ່າອອກຖາວອນໄດ້?
            'forceDelete' => $adminOnly, // ອາດຈະບໍ່ໃຊ້ Soft Deletes
        ];
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ override methods ເຫຼົ່ານີ້ຢູ່ບ່ອນນີ້.
}
