<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RequestPolicy  extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Request Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍຊື່ຄຳຮ້ອງໄດ້ (Admin/SA/Staff ທີ່ກ່ຽວຂ້ອງ)
        $viewListRoles = ['admin', 'school_admin', 'teacher', 'finance_staff']; // ປັບຕາມຄວາມເໝາະສົມ
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ/ຈັດການໄດ້ໂດຍກົງ (Admin/SA)
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງຄຳຮ້ອງໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $createRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຄຳຮ້ອງທັງໝົດໄດ້? (ຄວນ Filter ຕາມ Role/ໜ້າວຽກ ໃນ Controller)
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດຄຳຮ້ອງສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຍື່ນຄຳຮ້ອງ ຫຼື ຜູ້ດຳເນີນການ
                'exceptions' => ['isSubmitter', 'isHandler'],
            ],
            // ໃຜສາມາດສ້າງ (ຍື່ນ) ຄຳຮ້ອງໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles, // ຜູ້ໃຊ້ສ່ວນໃຫຍ່ຍື່ນໄດ້
                'permissions' => ['submit_requests'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຄຳຮ້ອງໄດ້? (ເຊັ່ນ: ປ່ຽນ status, ເພີ່ມ response)
            'update' => [
                'roles' => $managementRoles, // Admin, School Admin ແກ້ໄຂໄດ້
                'permissions' => ['manage_requests'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ດຳເນີນການທີ່ຖືກມອບໝາຍ
                'exceptions' => ['isHandler'],
                // ອາດຈະເພີ່ມ: ຜູ້ຍື່ນສາມາດແກ້ໄຂໄດ້ ຖ້າ status ຍັງເປັນ pending?
            ],
            // ໃຜສາມາດລຶບຄຳຮ້ອງໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin ລຶບໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ຍື່ນອາດຈະລຶບໄດ້ ຖ້າ status ຍັງເປັນ pending?
                'exceptions' => [], // ['isSubmitterAndPending']
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ຍື່ນຄຳຮ້ອງນີ້ ຫຼື ບໍ່.
     */
    protected function isSubmitter(User $user, Model $model): bool
    {
        if (!$model instanceof Request) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ user_id ຂອງ Request ຫຼືບໍ່
        return $user->user_id === $model->user_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ດຳເນີນການຂອງຄຳຮ້ອງນີ້ ຫຼື ບໍ່.
     */
    protected function isHandler(User $user, Model $model): bool
    {
        if (!$model instanceof Request) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ handled_by ຂອງ Request ຫຼືບໍ່
        return !is_null($model->handled_by) && $user->user_id === $model->handled_by;
    }

    // ສາມາດເພີ່ມ Exception method ອື່ນໆໄດ້ຕາມຕ້ອງການ ເຊັ່ນ:
    // protected function isSubmitterAndPending(User $user, Model $model): bool
    // {
    //     return $this->isSubmitter($user, $model) && $model->status === 'pending';
    // }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
