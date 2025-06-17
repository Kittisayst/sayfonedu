<?php

namespace App\Policies;

use App\Models\User;

class DigitalResourceAccessPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions.
     * ໝາຍເຫດ: Policy ນີ້ເນັ້ນຄວບຄຸມການເບິ່ງ Log ເປັນຫຼັກ.
     * ການ Create/Update Log ໂດຍທົ່ວໄປບໍ່ໄດ້ເຮັດຜ່ານ User action.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ Log ການເຂົ້າເຖິງໄດ້
        $viewLogRoles = ['admin', 'school_admin', 'librarian']; // ສົມມຸດວ່າມີ Role 'librarian'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບ Log ໄດ້ (ຈຳກັດທີ່ສຸດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ Log ການເຂົ້າເຖິງທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewLogRoles,
                'permissions' => ['view_library_logs'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດ Log ສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewLogRoles,
                'permissions' => ['view_library_logs'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງ Log record ໃໝ່ໄດ້? (ປົກກະຕິບໍ່ມີ)
            'create' => [
                'roles' => [], // ບໍ່ໃຫ້ໃຜສ້າງໂດຍກົງ
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂ Log record ໄດ້? (ປົກກະຕິບໍ່ມີ)
            'update' => [
                'roles' => [], // ບໍ່ໃຫ້ໃຜແກ້ໄຂ
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບ Log record ໄດ້? (ຄວນລະວັງ)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => ['manage_library_logs'], // ຕົວຢ່າງ permission
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

    // ບໍ່ຈຳເປັນຕ້ອງມີ Exception Methods ສະເພາະສຳລັບກົດເກນຂ້າງເທິງ

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
