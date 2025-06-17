<?php

namespace App\Policies;

use App\Models\User;

class ExaminationPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Examination Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການສອບເສັງທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນການສອບເສັງສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // ບໍ່ມີຂໍ້ຍົກເວັ້ນພິເສດ
            ],
            // ໃຜສາມາດສ້າງການສອບເສັງໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_examinations'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນການສອບເສັງໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_examinations'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນການສອບເສັງໄດ້? (Soft Delete)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນການສອບເສັງທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນການສອບເສັງອອກຖາວອນໄດ້?
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
