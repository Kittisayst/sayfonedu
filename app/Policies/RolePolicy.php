<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Role Policy.
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
            'delete' => $adminOnly, // ອາດຈະເພີ່ມ exception ຫ້າມລຶບ Role 'admin' ເອງໃນພາຍຫຼັງ
            'restore' => $adminOnly,
            'forceDelete' => $adminOnly, // ອາດຈະເພີ່ມ exception ຫ້າມລຶບ Role 'admin' ເອງໃນພາຍຫຼັງ
        ];
    }

    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ methods: viewAny, view, create, update, delete, restore, forceDelete
    // ເພາະມັນຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.

    // ຖ້າຕ້ອງການເງື່ອນໄຂພິເສດ ເຊັ່ນ ຫ້າມລຶບ Role 'admin', ສາມາດສ້າງ exception method:
    // protected function isNotAdminRole(User $user, Model $model): bool
    // {
    //     if (!$model instanceof Role) return false;
    //     return $model->role_name !== 'admin';
    // }
    // ແລ້ວໄປເພີ່ມ 'isNotAdminRole' ເຂົ້າໄປໃນ array 'exceptions' ຂອງ 'delete' ແລະ 'forceDelete'
    // ໃນ getRolesAndExceptionsConfig()
}
