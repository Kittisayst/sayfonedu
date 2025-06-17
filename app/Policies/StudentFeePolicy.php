<?php

namespace App\Policies;

use App\Models\StudentFee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentFeePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentFee Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ອາດຈະກວ້າງ)
        $viewRoles = ['admin', 'school_admin', 'finance_staff', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'finance_staff']; // Admin ແລະ ການເງິນ
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຄ່າທຳນຽມນັກຮຽນທັງໝົດໄດ້? (ຄວນ Filter ໃນ Controller/Resource)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດຄ່າທຳນຽມສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, Finance ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດສ້າງລາຍການຄ່າທຳນຽມໃໝ່ໄດ້? (ເຊັ່ນ: generate invoice)
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_student_fees'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂລາຍການຄ່າທຳນຽມໄດ້? (ເຊັ່ນ: ປັບ amount, discount, due_date)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_student_fees'], // ຕົວຢ່າງ permission
                'exceptions' => [], // Status ປົກກະຕິຈະຖືກອັບເດດຜ່ານ Payment
            ],
            // ໃຜສາມາດລຶບລາຍການຄ່າທຳນຽມໄດ້? (ຄວນລະວັງ)
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Fee record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentFee || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

     /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Fee record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentFee || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $model->student_id)->exists() ?? false;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
