<?php

namespace App\Policies;

use App\Models\ClassSubject;
use App\Models\StudentEnrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollmentPolicy extends AppPolicy
{
   /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentEnrollment Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້
        $managementRoles = ['admin', 'school_admin']; // ອາດຈະເພີ່ມ 'registrar'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການລົງທະບຽນທັງໝົດໄດ້? (ຄວນ Filter ໃນ Controller/Resource)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການລົງທະບຽນສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຄູທີ່ກ່ຽວຂ້ອງກັບຫ້ອງ, ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isTeacherOfEnrolledClass', 'isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດສ້າງການລົງທະບຽນໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_enrollments'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂການລົງທະບຽນໄດ້? (ເຊັ່ນ: ປ່ຽນ status, ຍ້າຍຫ້ອງ)
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_enrollments'], // ຕົວຢ່າງ permission
                'exceptions' => [], // ອາດຈະເພີ່ມ exception ໃຫ້ຄູປະຈຳຫ້ອງປ່ຽນ status ໄດ້
            ],
            // ໃຜສາມາດລຶບການລົງທະບຽນໄດ້?
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Enrollment record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEnrollment || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

     /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Enrollment record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEnrollment || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model
        // ແລະ Guardian->students relationship ມີຢູ່
        return $user->guardian?->students()->where('students.student_id', $model->student_id)->exists() ?? false;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ກ່ຽວຂ້ອງກັບຫ້ອງຮຽນຂອງ Enrollment record ນີ້ ຫຼື ບໍ່.
     */
    protected function isTeacherOfEnrolledClass(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEnrollment || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        $teacherId = $user->teacher?->getKey();
        if (!$teacherId) return false;

        // ສົມມຸດວ່າ StudentEnrollment model ມີ relationship 'schoolClass'
        $class = $model->schoolClass;
        if (!$class) return false;

        // ກວດສອບວ່າເປັນຄູປະຈຳຫ້ອງບໍ່
        if ($class->homeroom_teacher_id === $teacherId) {
            return true;
        }

        // ກວດສອບວ່າສອນວິຊາໃດໜຶ່ງໃນຫ້ອງນີ້ບໍ່ (ຜ່ານ ClassSubject)
        // ສົມມຸດ Model ClassSubject ມີຢູ່
        return ClassSubject::where('class_id', $class->getKey())
                           ->where('teacher_id', $teacherId)
                           ->exists();
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
