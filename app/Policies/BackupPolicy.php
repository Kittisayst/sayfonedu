<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\User;

class BackupPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Backup Policy.
     * ກຳນົດໃຫ້ທຸກ action ສາມາດເຮັດໄດ້ໂດຍ Role 'admin' ເທົ່ານັ້ນ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດໃຫ້ທຸກ action ຕ້ອງການ role 'admin'
        $adminOnly = [
            'roles' => ['admin'],
            'permissions' => ['manage_backups'], // ຕົວຢ່າງ permission (optional)
            'exceptions' => [],
        ];

        // ສົ່ງຄືນ config ທີ່ຄືກັນໝົດສຳລັບທຸກ standard actions
        // ແລະ ເພີ່ມ custom action 'download'
        return [
            'viewAny' => $adminOnly,
            'view' => $adminOnly,
            'create' => $adminOnly, // ສິດທິໃນການສັ່ງໃຫ້ລະບົບສ້າງ Backup ໃໝ່
            'update' => $adminOnly, // ປົກກະຕິບໍ່ມີການ update record backup
            'delete' => $adminOnly, // ສິດທິໃນການລຶບ record ແລະ/ຫຼື ໄຟລ໌ backup
            'restore' => $adminOnly, // ສິດທິໃນການສັ່ງກູ້ຄືນຈາກ Backup ນີ້ (ອາດຈະມີ logic ເພີ່ມ)
            'forceDelete' => $adminOnly, // ສົມມຸດບໍ່ໃຊ້ soft delete
            'download' => $adminOnly, // Custom action: ສິດທິໃນການດາວໂຫຼດໄຟລ໌ backup
        ];
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ສາມາດດາວໂຫຼດໄຟລ໌ backup ໄດ້ບໍ່.
     * (Custom policy method)
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Backup  $backup
     * @return bool
     */
    public function download(User $user, Backup $backup): bool
    {
        // ເອີ້ນໃຊ້ authorize helper ຈາກ AppPolicy ໂດຍໃຊ້ action 'download'
        return $this->authorize($user, 'download', $backup);
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ override methods ເຫຼົ່ານີ້ຢູ່ບ່ອນນີ້ສຳລັບກົດເກນ "Admin only".
}
