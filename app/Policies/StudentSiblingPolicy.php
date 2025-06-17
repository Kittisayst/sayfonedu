<?php

namespace App\Policies;

use App\Models\StudentSibling;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentSiblingPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentSibling Policy.
     * ເນັ້ນການຄວບຄຸມການເບິ່ງ ແລະ ລຶບການເຊື່ອມໂຍງ. ການສ້າງ/ແກ້ໄຂມັກຈະເຮັດຜ່ານ Student Profile.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher']; // Student/Parent ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການເຊື່ອມໂຍງພີ່ນ້ອງທັງໝົດ (ຂອງນັກຮຽນຄົນໃດຄົນໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // Context check likely in controller
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການເຊື່ອມໂຍງສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນທີ່ຢູ່ໃນການເຊື່ອມໂຍງ ຫຼື ຜູ້ປົກຄອງຂອງນັກຮຽນເຫຼົ່ານັ້ນ
                'exceptions' => ['isEitherSibling', 'isGuardianOfEitherSibling'],
            ],
            // ໃຜສາມາດສ້າງການເຊື່ອມໂຍງໃໝ່ໄດ້? (ປົກກະຕິເຮັດຜ່ານ Student Profile)
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_student_profile'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂການເຊື່ອມໂຍງໄດ້? (ເຊັ່ນ: ປ່ຽນ relationship type)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_student_profile'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບການເຊື່ອມໂຍງພີ່ນ້ອງໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                'exceptions' => [], // ອາດຈະເພີ່ມ exception ໃຫ້ຜູ້ກ່ຽວຂ້ອງລຶບໄດ້?
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນຄົນໃດຄົນໜຶ່ງໃນ Sibling record ນີ້ ຫຼື ບໍ່.
     */
    protected function isEitherSibling(User $user, Model $model): bool
    {
        if (!$model instanceof StudentSibling || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        $loggedInStudentId = $user->student?->student_id;
        if (!$loggedInStudentId) return false;

        // ກວດສອບວ່າ ID ຂອງຜູ້ໃຊ້ທີ່ login ກົງກັບ student_id ຫຼື sibling_student_id ໃນ record ຫຼືບໍ່
        return $loggedInStudentId === $model->student_id || $loggedInStudentId === $model->sibling_student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນຄົນໃດຄົນໜຶ່ງໃນ Sibling record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfEitherSibling(User $user, Model $model): bool
    {
        if (!$model instanceof StudentSibling || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        $guardian = $user->guardian;
        if (!$guardian) return false;

        // ກວດສອບວ່າຜູ້ປົກຄອງຄົນນີ້ ເປັນຜູ້ປົກຄອງຂອງ student_id ຫຼື sibling_student_id ຫຼືບໍ່
        return $guardian->students()
            ->whereIn('students.student_id', [$model->student_id, $model->sibling_student_id])
            ->exists();
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
