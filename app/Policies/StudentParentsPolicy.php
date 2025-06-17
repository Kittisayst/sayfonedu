<?php

namespace App\Policies;

use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentParentsPolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Parent/Guardian Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຜູ້ປົກຄອງທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => ['admin', 'school_admin', 'teacher'], // Admin, School Admin, Teacher ເບິ່ງລາຍຊື່ໄດ້
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນຜູ້ປົກຄອງສະເພາະຄົນໄດ້?
            'view' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile (ຖ້າມີ user_id ຜູກ), ຄູຂອງລູກ (ອາດຈະເພີ່ມ exception ນີ້)
                'exceptions' => ['isOwner'],
            ],
            // ໃຜສາມາດສ້າງຂໍ້ມູນຜູ້ປົກຄອງໃໝ່ໄດ້?
            'create' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin (ຫຼື Registrar)
                'permissions' => ['create_parents'], // ໃຊ້ permission (ຖ້າມີ)
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຜູ້ປົກຄອງໄດ້?
            'update' => [
                'roles' => ['admin', 'school_admin'], // Admin, School Admin ແກ້ໄຂໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ເຈົ້າຂອງ profile (ຖ້າມີ user_id ຜູກ)
                'exceptions' => ['isOwner'],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນຜູ້ປົກຄອງໄດ້?
            'delete' => [
                'roles' => ['admin'], // ຈຳກັດສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດກູ້ຄືນຂໍ້ມູນຜູ້ປົກຄອງທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຂໍ້ມູນຜູ້ປົກຄອງອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນເຈົ້າຂອງ Parent/Guardian record ນີ້ ຫຼື ບໍ່
     * (ໂດຍກວດສອບຜ່ານ user_id ທີ່ຜູກກັນ).
     *
     * @param  \App\Models\User  $user The current user.
     * @param  \Illuminate\Database\Eloquent\Model  $model The StudentParents model being accessed.
     * @return bool
     */
    protected function isOwner(User $user, Model $model): bool
    {
        // ກວດສອບກ່ອນວ່າ model ເປັນ instance ຂອງ StudentParents ແທ້
        if (!$model instanceof StudentParent) {
            return false;
        }
        // ກວດສອບວ່າ Parent record ມີ user_id ຜູກຢູ່ ແລະ ກົງກັບ user ທີ່ login ຫຼືບໍ່
        return !is_null($model->user_id) && $user->user_id === $model->user_id;
    }

    // ສາມາດເພີ່ມ Exception methods ອື່ນໆໄດ້ຕາມຕ້ອງການ ເຊັ່ນ:
    // protected function isTeacherOfGuardiansStudent(User $user, Model $model): bool
    // {
    //     // Logic ກວດສອບວ່າ $user ເປັນຄູຂອງນັກຮຽນທີ່ $model ເປັນຜູ້ປົກຄອງ ຫຼື ບໍ່
    // }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
