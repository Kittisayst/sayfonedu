<?php

namespace App\Policies;

use App\Models\ExtracurricularActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ExtracurricularActivityPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ ExtracurricularActivity Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $viewRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງກິດຈະກຳໄດ້
        $createRoles = ['admin', 'school_admin', 'teacher']; // ອະນຸຍາດໃຫ້ຄູສ້າງກິດຈະກຳໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດແກ້ໄຂໄດ້ (ນອກຈາກຜູ້ປະສານງານ)
        $updateRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ກິດຈະກຳທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດກິດຈະກຳສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles, // ທຸກຄົນເບິ່ງລາຍລະອຽດໄດ້
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດສ້າງກິດຈະກຳໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['manage_activities'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂກິດຈະກຳໄດ້?
            'update' => [
                'roles' => $updateRoles, // Admin, School Admin
                'permissions' => ['manage_activities'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ປະສານງານກິດຈະກຳ
                'exceptions' => ['isCoordinator'],
            ],
            // ໃຜສາມາດລຶບກິດຈະກຳໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                // ອາດຈະເພີ່ມ exception ໃຫ້ຜູ້ປະສານງານລຶບໄດ້?
                'exceptions' => [], // ['isCoordinator']
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປະສານງານ (Coordinator) ຂອງກິດຈະກຳນີ້ ຫຼື ບໍ່.
     */
    protected function isCoordinator(User $user, Model $model): bool
    {
        if (!$model instanceof ExtracurricularActivity) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ coordinator_id ຂອງ Activity ຫຼືບໍ່
        // (ອີງຕາມ definition D56, coordinator_id ເກັບ user_id)
        return !is_null($model->coordinator_id) && $user->user_id === $model->coordinator_id;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
