<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TeacherPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Teacher Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຄູສອນທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => ['admin', 'school_admin', 'teacher'], // Admin, School Admin, ແລະ Teacher ເບິ່ງລາຍຊື່ໄດ້
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນຄູສອນສະເພາະຄົນໄດ້?
            'view' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin ເບິ່ງໄດ້ໝົດ
                'permissions' => [],
                'exceptions' => ['isOwner'], // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile ເບິ່ງເອງໄດ້
            ],
            // ໃຜສາມາດສ້າງຂໍ້ມູນຄູສອນໃໝ່ໄດ້?
            'create' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin ສ້າງໄດ້
                'permissions' => ['create_teachers'], // ຫຼື ໃຊ້ permission (ຖ້າມີ)
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຄູສອນໄດ້?
            'update' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin ແກ້ໄຂໄດ້ໝົດ
                'permissions' => [],
                'exceptions' => ['isOwner'], // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile ແກ້ໄຂເອງໄດ້
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນຄູສອນໄດ້?
            'delete' => [
                'roles' => ['admin'], // ສະເພາະ Admin ເພື່ອຄວາມປອດໄພ (ຫຼື ເພີ່ມ school_admin ຖ້າຕ້ອງການ)
                'permissions' => [],
                'exceptions' => [], // ບໍ່ມີເງື່ອນໄຂຫ້າມລຶບຕົນເອງໂດຍກົງກັບ Teacher record
            ],
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນຄູສອນທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນຄູສອນອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນເຈົ້າຂອງ Teacher record ນີ້ ຫຼື ບໍ່
     * (ໂດຍກວດສອບຜ່ານ user_id ທີ່ຜູກກັນ).
     *
     * @param  \App\Models\User  $user The current user.
     * @param  \Illuminate\Database\Eloquent\Model  $model The Teacher model being accessed.
     * @return bool
     */
    protected function isOwner(User $user, Model $model): bool
    {
        // ກວດສອບກ່ອນວ່າ model ເປັນ instance ຂອງ Teacher ແທ້
        if (!$model instanceof Teacher) {
            return false;
        }
        // ກວດສອບວ່າ user_id ຂອງ Teacher record ກົງກັບ user_id ຂອງຜູ້ໃຊ້ທີ່ login ຫຼືບໍ່
        return $user->user_id === $model->user_id;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
