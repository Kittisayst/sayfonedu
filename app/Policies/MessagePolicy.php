<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MessagePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Message Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ/ຈັດການໄດ້ໂດຍກົງ (Admin/SA)
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດສົ່ງຂໍ້ຄວາມໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $sendingRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບຂໍ້ຄວາມໄດ້ (ນອກຈາກເຈົ້າຂອງ)
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຂໍ້ຄວາມ *ທັງໝົດ* ໄດ້? (ປົກກະຕິບໍ່ມີ, ຈຳກັດໃຫ້ Admin)
            'viewAny' => [
                'roles' => $managementRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ຄວາມສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ສົ່ງ ຫຼື ຜູ້ຮັບ
                'exceptions' => ['isSender', 'isReceiver'],
            ],
            // ໃຜສາມາດສ້າງ (ສົ່ງ) ຂໍ້ຄວາມໃໝ່ໄດ້?
            'create' => [
                'roles' => $sendingRoles, // ຜູ້ໃຊ້ສ່ວນໃຫຍ່ສົ່ງໄດ້
                'permissions' => ['send_messages'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ຄວາມໄດ້? (ປົກກະຕິບໍ່ຄວນແກ້ໄຂໄດ້)
            'update' => [
                'roles' => ['admin'], // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ (ຫຼື ບໍ່ໃຫ້ເລີຍ)
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ຄວາມໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin ລຶບໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ສົ່ງ ຫຼື ຜູ້ຮັບ ສາມາດລຶບ (ອາດຈະເປັນ soft delete ໃນ inbox/sentbox)
                'exceptions' => ['isSender', 'isReceiver'],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ສົ່ງຂໍ້ຄວາມນີ້ ຫຼື ບໍ່.
     */
    protected function isSender(User $user, Model $model): bool
    {
        if (!$model instanceof Message) {
            return false;
        }
        return $user->user_id === $model->sender_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຮັບຂໍ້ຄວາມນີ້ ຫຼື ບໍ່.
     */
    protected function isReceiver(User $user, Model $model): bool
    {
        if (!$model instanceof Message) {
            return false;
        }
        return $user->user_id === $model->receiver_id;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
