<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AnnouncementPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Announcement Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍຊື່ປະກາດໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $viewListRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'school_admin']; // ອາດຈະເພີ່ມ Role ອື່ນເຊັ່ນ 'communications_officer'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin']; // ອະນຸຍາດໃຫ້ຜູ້ສ້າງລຶບຜ່ານ exception

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ປະກາດທັງໝົດໄດ້? (ການ filter ຕາມ target_group/date ເຮັດໃນ Controller)
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງປະກາດສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້ໝົດ
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ໃຊ້ທົ່ວໄປສາມາດເບິ່ງໄດ້ຖ້າປະກາດນັ້ນເໝາະສົມກັບກຸ່ມເປົ້າໝາຍ
                'exceptions' => ['isVisibleToUser'],
            ],
            // ໃຜສາມາດສ້າງປະກາດໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['create_announcements'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂປະກາດໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_announcements'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ສ້າງເດີມສາມາດແກ້ໄຂໄດ້
                'exceptions' => ['isCreator'],
            ],
            // ໃຜສາມາດລຶບປະກາດໄດ້?
            'delete' => [
                'roles' => $deletionRoles,
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ສ້າງເດີມສາມາດລຶບໄດ້
                'exceptions' => ['isCreator'],
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
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ສ້າງ Announcement record ນີ້ ຫຼື ບໍ່.
     */
    protected function isCreator(User $user, Model $model): bool
    {
        if (!$model instanceof Announcement) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ created_by ຫຼືບໍ່
        return $user->user_id === $model->created_by;
    }

    /**
     * Exception method: ກວດສອບວ່າ Announcement ນີ້ສາມາດເຫັນໄດ້ໂດຍຜູ້ໃຊ້ຄົນນີ້ ຫຼື ບໍ່ (ອີງຕາມ target_group).
     */
    protected function isVisibleToUser(User $user, Model $model): bool
    {
        if (!$model instanceof Announcement) {
            return false;
        }

        // ກວດສອບວັນທີກ່ອນ (ຖ້າຕ້ອງການໃຫ້ກວດໃນ Policy)
        // $today = Carbon::today()->toDateString();
        // $startDateOk = is_null($model->start_date) || $model->start_date->toDateString() <= $today;
        // $endDateOk = is_null($model->end_date) || $model->end_date->toDateString() >= $today;
        // if (!($startDateOk && $endDateOk)) {
        //     return false; // ບໍ່ຢູ່ໃນຊ່ວງເວລາສະແດງຜົນ
        // }

        // ກວດສອບ Target Group
        if ($model->target_group === 'all') {
            return true; // ທຸກຄົນເຫັນໄດ້
        }

        // ກວດສອບ Role ຂອງຜູ້ໃຊ້
        if ($user->role) {
            // ສ້າງ Map ເພື່ອແປງຊື່ Role ໃຫ້ກົງກັບຄ່າໃນ target_group (ຖ້າຈຳເປັນ)
            $roleToTargetMap = [
                'teacher' => 'teachers',
                'student' => 'students',
                'parent'  => 'parents',
                // ເພີ່ມ Role ອື່ນໆຖ້າ target_group ມີຄ່າທີ່ຕ່າງຈາກຊື່ Role
            ];
            $userTargetGroup = $roleToTargetMap[$user->role->role_name] ?? $user->role->role_name; // ໃຊ້ຊື່ Role ເອງຖ້າບໍ່ມີໃນ Map

            if ($userTargetGroup === $model->target_group) {
                return true; // ກົງກັບກຸ່ມເປົ້າໝາຍ
            }
        }

        return false; // ບໍ່ກົງກັບເງື່ອນໄຂໃດເລີຍ
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
