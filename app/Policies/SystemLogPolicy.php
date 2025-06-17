<?php

namespace App\Policies;

use App\Models\User;

class SystemLogPolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ SystemLog Policy.
     * ກຳນົດໃຫ້ການເຂົ້າເຖິງສ່ວນໃຫຍ່ສາມາດເຮັດໄດ້ໂດຍ Role 'admin' ເທົ່ານັ້ນ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດໃຫ້ action ສ່ວນໃຫຍ່ຕ້ອງການ role 'admin'
        $adminOnly = [
            'roles' => ['admin'],
            'permissions' => ['view_system_logs'], // ຕົວຢ່າງ permission (optional)
            'exceptions' => [],
        ];
        // ກຳນົດວ່າບໍ່ມີໃຜສາມາດ Create/Update Log ຜ່ານ Policy ນີ້ໄດ້
        $noOne = [
            'roles' => [],
            'permissions' => [],
            'exceptions' => [],
        ];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ Log ທັງໝົດໄດ້?
            'viewAny' => $adminOnly,
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດ Log ສະເພາະອັນໄດ້?
            'view' => $adminOnly,
            // ໃຜສາມາດສ້າງ Log ໃໝ່ໄດ້? (ລະບົບເປັນຜູ້ສ້າງ)
            'create' => $noOne,
            // ໃຜສາມາດແກ້ໄຂ Log ໄດ້? (ບໍ່ຄວນແກ້ໄຂ)
            'update' => $noOne,
            // ໃຜສາມາດລຶບ Log ໄດ້? (ອາດຈະເປັນການ Clear Log)
            'delete' => $adminOnly,
            // Restore/ForceDelete (ຕາຕະລາງນີ້ອາດຈະບໍ່ໃຊ້ Soft Deletes)
            'restore' => $adminOnly,
            'forceDelete' => $adminOnly,
        ];
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ override methods ເຫຼົ່ານີ້ຢູ່ບ່ອນນີ້ສຳລັບກົດເກນ "Admin only".
}
