<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Payment Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'finance_staff']; // Admin, School Admin, Finance ເບິ່ງໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'finance_staff']; // Admin ແລະ ການເງິນ
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດທີ່ສຸດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການຊຳລະເງິນທັງໝົດໄດ້? (ຄວນ Filter ໃນ Controller/Resource)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການຊຳລະເງິນສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Finance ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນທີ່ກ່ຽວຂ້ອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນນັ້ນ
                'exceptions' => ['isOwnerOfRelatedFee', 'isGuardianOfRelatedStudent'],
            ],
            // ໃຜສາມາດສ້າງບັນທຶກການຊຳລະເງິນໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['record_payments'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກການຊຳລະເງິນໄດ້? (ເຊັ່ນ: ຢືນຢັນ, ເພີ່ມໝາຍເຫດ)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_payments'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ບັນທຶກເດີມອາດຈະແກ້ໄຂໄດ້
                'exceptions' => ['isRecorder'],
            ],
            // ໃຜສາມາດລຶບບັນທຶກການຊຳລະເງິນໄດ້? (ປົກກະຕິບໍ່ຄວນລຶບ)
            'delete' => [
                'roles' => $deletionRoles, // ຈຳກັດໃຫ້ Admin ເທົ່ານັ້ນ
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນທີ່ກ່ຽວຂ້ອງກັບ Fee ທີ່ Payment ນີ້ຈ່າຍໃຫ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRelatedFee(User $user, Model $model): bool
    {
        if (!$model instanceof Payment || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ Payment model ມີ relationship 'studentFee'
        $studentFee = $model->studentFee; // ຄວນ eager load ຖ້າໃຊ້ໃນ list
        if (!$studentFee) return false;

        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $studentFee->student_id;
    }

     /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນທີ່ກ່ຽວຂ້ອງກັບ Fee ທີ່ Payment ນີ້ຈ່າຍໃຫ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfRelatedStudent(User $user, Model $model): bool
    {
        if (!$model instanceof Payment || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
             return false;
        }

        // ສົມມຸດວ່າ Payment model ມີ relationship 'studentFee'
        $studentFee = $model->studentFee; // ຄວນ eager load ຖ້າໃຊ້ໃນ list
        if (!$studentFee) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (ທີ່ເຮົາປ່ຽນຊື່ເປັນ StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        return $user->guardian?->students()->where('students.student_id', $studentFee->student_id)->exists() ?? false;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ບັນທຶກ Payment record ນີ້ ຫຼື ບໍ່.
     */
    protected function isRecorder(User $user, Model $model): bool
    {
        if (!$model instanceof Payment) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ received_by ຫຼືບໍ່
        return !is_null($model->received_by) && $user->user_id === $model->received_by;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
