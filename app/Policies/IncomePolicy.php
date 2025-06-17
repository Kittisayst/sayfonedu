<?php

namespace App\Policies;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class IncomePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Income Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'finance_staff']; // Admin ແລະ ການເງິນ
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ລາຍຮັບທັງໝົດໄດ້? (ຄວນ Filter)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດລາຍຮັບສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Finance ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຮັບ/ບັນທຶກລາຍການນັ້ນ
                'exceptions' => ['isReceiver'],
            ],
            // ໃຜສາມາດສ້າງລາຍການລາຍຮັບໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['record_income'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂລາຍການລາຍຮັບໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_income'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ຮັບ/ບັນທຶກເດີມອາດຈະແກ້ໄຂໄດ້
                'exceptions' => ['isReceiver'],
            ],
            // ໃຜສາມາດລຶບລາຍການລາຍຮັບໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin
                'permissions' => [],
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

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຮັບ/ບັນທຶກ Income record ນີ້ ຫຼື ບໍ່.
     */
    protected function isReceiver(User $user, Model $model): bool
    {
        if (!$model instanceof Income) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ received_by ຫຼືບໍ່
        return $user->user_id === $model->received_by;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
