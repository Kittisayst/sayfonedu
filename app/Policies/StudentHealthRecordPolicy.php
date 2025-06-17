<?php

namespace App\Policies;

use App\Models\StudentHealthRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentHealthRecordPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentHealthRecord Policy.
     * ຕ້ອງຈຳກັດການເຂົ້າເຖິງຂໍ້ມູນສຸຂະພາບຢ່າງເຂັ້ມງວດ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍຊື່ໄດ້ (ຈຳກັດ)
        $viewListRoles = ['admin', 'school_admin', 'nurse']; // ສົມມຸດວ່າມີ Role 'nurse'
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ record ສະເພາະໄດ້
        $viewRecordRoles = ['admin', 'school_admin', 'nurse']; // Student/Parent ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'school_admin', 'nurse']; // Admin, School Admin, Nurse
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດທີ່ສຸດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຂໍ້ມູນສຸຂະພາບທັງໝົດໄດ້? (ຄວນຈຳກັດ ແລະ Filter)
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງຂໍ້ມູນສຸຂະພາບສະເພາະຄົນໄດ້?
            'view' => [
                'roles' => $viewRecordRoles, // Role ທີ່ກ່ຽວຂ້ອງໂດຍກົງ
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດສ້າງບັນທຶກສຸຂະພາບໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_health_records'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກສຸຂະພາບໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_health_records'], // ຕົວຢ່າງ permission
                'exceptions' => [], // ບໍ່ຄວນໃຫ້ ນັກຮຽນ/ຜູ້ປົກຄອງ ແກ້ໄຂໂດຍກົງ
            ],
            // ໃຜສາມາດລຶບບັນທຶກສຸຂະພາບໄດ້?
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Health record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentHealthRecord || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Health record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentHealthRecord || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດ StudentHealthRecord model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $student->student_id)->exists() ?? false;
    }

    /**
     * Helper method: ກວດສອບວ່າຜູ້ໃຊ້ເປັນພະຍາບານ ຫຼື ບໍ່ (ຕົວຢ່າງ).
     * (ຫຼື ຈະເອົາໄປໄວ້ໃນ AppPolicy ຖ້າໃຊ້ຫຼາຍບ່ອນ)
     */
    protected function isNurse(User $user): bool
    {
        // ປັບຕາມການກວດສອບ Role ຕົວຈິງຂອງທ່ານ
        return $user->role && $user->role->role_name === 'nurse';
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
