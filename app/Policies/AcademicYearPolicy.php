<?php

namespace App\Policies;

use App\Models\User;

class AcademicYearPolicy extends AppPolicy
{
  /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ AcademicYear Policy.
     * ອະນຸຍາດໃຫ້ 'admin' ແລະ 'school_admin' ເຂົ້າເຖິງສ່ວນໃຫຍ່, ແຕ່ຈຳກັດການລຶບໃຫ້ 'admin'.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການຂໍ້ມູນສ່ວນໃຫຍ່ໄດ້
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບຂໍ້ມູນໄດ້ (ອາດຈະຈຳກັດກວ່າ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ສົກຮຽນທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $managementRoles,
                'permissions' => [], // ບໍ່ໃຊ້ permission ສະເພາະໃນຕົວຢ່າງນີ້
                'exceptions' => [], // ບໍ່ມີຂໍ້ຍົກເວັ້ນ
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນສົກຮຽນສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງສົກຮຽນໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_academic_data'], // ຕົວຢ່າງການໃຊ້ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນສົກຮຽນໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_academic_data'], // ຕົວຢ່າງການໃຊ້ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນສົກຮຽນໄດ້? (Soft Delete)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນສົກຮຽນທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນສົກຮຽນອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ override methods ເຫຼົ່ານີ້ຢູ່ບ່ອນນີ້.
}
