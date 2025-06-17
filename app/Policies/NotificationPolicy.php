<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Notification Policy.
     * Notifications ໂດຍທົ່ວໄປແມ່ນອ່ານ ແລະ ລຶບໄດ້ໂດຍຜູ້ຮັບ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ/ຈັດການໄດ້ໂດຍກົງ (Admin/SA)
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ນອກຈາກເຈົ້າຂອງ)
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ Notification *ທັງໝົດ* ໄດ້? (ປົກກະຕິບໍ່ມີ, ຈຳກັດໃຫ້ Admin)
            'viewAny' => [
                'roles' => $managementRoles,
                'permissions' => [],
                'exceptions' => [], // ຜູ້ໃຊ້ທົ່ວໄປຈະເບິ່ງລາຍຊື່ຂອງຕົນເອງຜ່ານ Controller
            ],
            // ໃຜສາມາດເບິ່ງ Notification ສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຮັບ Notification ນັ້ນ
                'exceptions' => ['isRecipient'],
            ],
            // ໃຜສາມາດສ້າງ Notification ໃໝ່ໄດ້? (ປົກກະຕິລະບົບຈະສ້າງໃຫ້)
            'create' => [
                'roles' => ['admin'], // ອາດຈະໃຫ້ Admin ສ້າງໄດ້ກໍລະນີພິເສດ
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂ Notification ໄດ້? (ປົກກະຕິບໍ່ມີການແກ້ໄຂ, ອາດຈະມີແຕ່ການ mark as read)
            'update' => [
                'roles' => ['admin'], // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບ Notification ໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin ລຶບໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຮັບ Notification ນັ້ນສາມາດລຶບຂອງຕົນເອງໄດ້
                'exceptions' => ['isRecipient'],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຮັບ Notification ນີ້ ຫຼື ບໍ່.
     */
    protected function isRecipient(User $user, Model $model): bool
    {
        if (!$model instanceof Notification) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ user_id ຂອງ Notification ຫຼືບໍ່
        return $user->user_id === $model->user_id;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
