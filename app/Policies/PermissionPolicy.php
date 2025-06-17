<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Permission Policy.
     * ກຳນົດໃຫ້ທຸກ action ສາມາດເຮັດໄດ້ໂດຍ Role 'admin' ເທົ່ານັ້ນ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດໃຫ້ທຸກ action ຕ້ອງການ role 'admin'
        $adminOnly = [
            'roles' => ['admin'],
            'permissions' => [],
            'exceptions' => [],
        ];

        // ສົ່ງຄືນ config ທີ່ຄືກັນໝົດສຳລັບທຸກ standard actions
        return [
            'viewAny' => $adminOnly,
            'view' => $adminOnly,
            'create' => $adminOnly,
            'update' => $adminOnly,
            'delete' => $adminOnly,
            'restore' => $adminOnly, // ອາດຈະບໍ່ກ່ຽວຂ້ອງກັບ Permission
            'forceDelete' => $adminOnly, // ອາດຈະບໍ່ກ່ຽວຂ້ອງກັບ Permission
        ];
    }

    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ methods: viewAny, view, create, update, delete, restore, forceDelete
    // ເພາະມັນຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
