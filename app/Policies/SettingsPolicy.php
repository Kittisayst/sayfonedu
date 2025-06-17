<?php

namespace App\Policies;

use App\Models\User;

class SettingsPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Settings Policy.
     * ກຳນົດໃຫ້ທຸກ action ສາມາດເຮັດໄດ້ໂດຍ Role 'admin' ເທົ່ານັ້ນ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດໃຫ້ທຸກ action ຕ້ອງການ role 'admin'
        $adminOnly = [
            'roles' => ['admin'],
            'permissions' => ['manage_settings'], // ຕົວຢ່າງ permission (optional)
            'exceptions' => [],
        ];

        // ສົ່ງຄືນ config ທີ່ຄືກັນໝົດສຳລັບທຸກ standard actions
        return [
            'viewAny' => $adminOnly,
            'view' => $adminOnly,
            'create' => $adminOnly,
            'update' => $adminOnly,
            'delete' => $adminOnly, // ອາດຈະເພີ່ມ exception ຫ້າມລຶບ setting ທີ່ is_system=true
            'restore' => $adminOnly, // ອາດຈະບໍ່ກ່ຽວຂ້ອງ
            'forceDelete' => $adminOnly, // ອາດຈະບໍ່ກ່ຽວຂ້ອງ
        ];
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ override methods ເຫຼົ່ານີ້ຢູ່ບ່ອນນີ້ສຳລັບກົດເກນ "Admin only".
}
