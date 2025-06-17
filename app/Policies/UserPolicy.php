<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ User Policy.
     * Logic ການກວດສອບຫຼັກຈະຢູ່ໃນ authorize() method ຂອງ AppPolicy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຜູ້ໃຊ້ທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => ['admin', 'school_admin'], // Admin & School Admin ເບິ່ງໄດ້
                'permissions' => [], // ບໍ່ມີ permission ສະເພາະ
                'exceptions' => [], // ບໍ່ມີເງື່ອນໄຂຍົກເວັ້ນ
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນຜູ້ໃຊ້ສະເພາະຄົນໄດ້?
            'view' => [
                'roles' => ['admin', 'school_admin'], // Admin & School Admin ເບິ່ງໄດ້
                'permissions' => [],
                'exceptions' => ['isOwner'], // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile ເບິ່ງເອງໄດ້
            ],
            // ໃຜສາມາດສ້າງຜູ້ໃຊ້ໃໝ່ໄດ້?
            'create' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => ['create_users'], // ຫຼື ໃຊ້ permission 'create_users' (ຖ້າມີ)
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ໄດ້?
            'update' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [],
                'exceptions' => ['isOwner'], // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile ແກ້ໄຂເອງໄດ້
            ],
            // ໃຜສາມາດລຶບຜູ້ໃຊ້ໄດ້?
            'delete' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => ['isNotSelf'], // ຍົກເວັ້ນ: ຫ້າມລຶບຕົນເອງ (Admin ກໍ່ລຶບຕົນເອງບໍ່ໄດ້)
            ],
            // ໃຜສາມາດກູ້ຄືນຜູ້ໃຊ້ທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຜູ້ໃຊ້ອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => ['isNotSelf'], // ຍົກເວັ້ນ: ຫ້າມລຶບຕົນເອງ
            ],
        ];
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ເປັນເຈົ້າຂອງ profile ຫຼືບໍ່.
     * ເຮົາ override method ນີ້ຈາກ AppPolicy ເພື່ອໃຊ້ User type hint ສະເພາະ.
     * (ຫຼື ຈະໃຊ້ isOwner ຈາກ AppPolicy ເລີຍກໍ່ໄດ້ ຖ້າມັນກວດສອບ user_id ຖືກຕ້ອງແລ້ວ)
     *
     * @param  \App\Models\User  $user The current user.
     * @param  \Illuminate\Database\Eloquent\Model  $model The user profile being accessed.
     * @return bool
     */
    protected function isOwner(User $user, Model $model): bool
    {
        // ກວດສອບກ່ອນວ່າ model ເປັນ instance ຂອງ User ແທ້
        if (!$model instanceof User) {
            return false;
        }
        // ກວດສອບ ID
        return $user->user_id === $model->user_id;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ກຳລັງພະຍາຍາມລຶບຕົນເອງ ຫຼື ບໍ່.
     * Method ນີ້ບໍ່ມີໃນ AppPolicy, ເຮົາສ້າງຂຶ້ນມາໃໝ່ສຳລັບ UserPolicy.
     *
     * @param  \App\Models\User  $user The current user.
     * @param  \Illuminate\Database\Eloquent\Model  $model The user profile being deleted.
     * @return bool Returns true ຖ້າ **ບໍ່ແມ່ນ** ການລຶບຕົນເອງ (ອະນຸຍາດໃຫ້ລຶບ).
     */
    protected function isNotSelf(User $user, Model $model): bool
    {
        // ກວດສອບກ່ອນວ່າ model ເປັນ instance ຂອງ User ແທ້
        if (!$model instanceof User) {
            return false; // Exception ບໍ່ຜ່ານ
        }
        // Exception ຈະຜ່ານ (return true) ກໍ່ຕໍ່ເມື່ອ ID ບໍ່ກົງກັນ
        return $user->user_id !== $model->user_id;
    }

    // ບໍ່ຈຳເປັນຕ້ອງຂຽນ methods: viewAny, view, create, update, delete, restore, forceDelete
    // ເພາະມັນຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້ແລ້ວ.
}
