<?php

namespace App\Policies;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SchoolClassPolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ SchoolClass Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຫ້ອງຮຽນທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => ['admin', 'school_admin', 'teacher'], // Admin, School Admin, Teacher ເບິ່ງລາຍຊື່ໄດ້
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນຫ້ອງຮຽນສະເພາະຫ້ອງໄດ້?
            'view' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [],
                // ຍົກເວັ້ນ: ຄູປະຈຳຫ້ອງ, ຄູທີ່ສອນໃນຫ້ອງນັ້ນ
                'exceptions' => ['isHomeroomTeacher', 'teachesInClass'],
                // ອາດຈະເພີ່ມ exception ສໍາລັບນັກຮຽນ/ຜູ້ປົກຄອງທີ່ຢູ່ໃນຫ້ອງນັ້ນ
            ],
            // ໃຜສາມາດສ້າງຫ້ອງຮຽນໃໝ່ໄດ້?
            'create' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['manage_classes'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຫ້ອງຮຽນໄດ້?
            'update' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['manage_classes'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຄູປະຈຳຫ້ອງ (ອາດຈະແກ້ໄຂໄດ້ບາງສ່ວນ)
                'exceptions' => ['isHomeroomTeacher'],
            ],
            // ໃຜສາມາດລຶບຫ້ອງຮຽນໄດ້?
            'delete' => [
                'roles' => ['admin'], // ຈຳກັດສະເພາະ Admin (ເພາະມີຂໍ້ມູນອື່ນຜູກຢູ່ຫຼາຍ)
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດກູ້ຄືນຫ້ອງຮຽນທີ່ຖືກລຶບໄດ້?
            'restore' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບຫ້ອງຮຽນອອກຖາວອນໄດ້?
            'forceDelete' => [
                'roles' => ['admin'], // ສະເພາະ Admin
                'permissions' => [],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ສອນວິຊາໃດໜຶ່ງໃນຫ້ອງນີ້ ຫຼື ບໍ່.
     * (ກວດສອບຜ່ານຕາຕະລາງ class_subjects)
     *
     * @param  \App\Models\User  $user The current user.
     * @param  \Illuminate\Database\Eloquent\Model  $model The SchoolClass model being accessed.
     * @return bool
     */
    protected function teachesInClass(User $user, Model $model): bool
    {
        // ກວດສອບວ່າ $model ເປັນ SchoolClass ແລະ $user ເປັນ Teacher
        if (!$model instanceof SchoolClass || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ກວດສອບໃນຕາຕະລາງ class_subjects ວ່າ ຄູຄົນນີ້ (user->teacher->teacher_id)
        // ຖືກມອບໝາຍໃຫ້ສອນໃນຫ້ອງນີ້ (model->class_id) ຫຼືບໍ່
        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        // ສົມມຸດວ່າ Model ClassSubject ມີຢູ່
        return \App\Models\ClassSubject::where('class_id', $model->getKey())
                                       ->where('teacher_id', $user->teacher?->getKey())
                                       ->exists();

        // ອີກທາງເລືອກໜຶ່ງ: ກວດສອບຜ່ານຕາຕະລາງ Schedules (ອາດຈະຊ້າກວ່າ ແຕ່ກວດສອບ is_active ໄດ້)
        // return \App\Models\Schedule::where('class_id', $model->getKey())
        //                            ->where('academic_year_id', $model->academic_year_id) // ຄວນກວດສອບສົກຮຽນນຳ
        //                            ->where('teacher_id', $user->teacher?->getKey())
        //                            ->where('is_active', true)
        //                            ->exists();
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
