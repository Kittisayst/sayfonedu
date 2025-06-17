<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\StudentSpecialNeed;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentSpecialNeedPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentSpecialNeed Policy.
     * ຂໍ້ມູນຄວາມຕ້ອງການພິເສດຄວນຖືກຈຳກັດການເຂົ້າເຖິງ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ຈຳກັດ)
        $viewRoles = ['admin', 'school_admin', 'teacher']; // ອາດຈະເພີ່ມ 'counselor', 'special_ed_teacher'
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ໄດ້ (ຈຳກັດ)
        $managementRoles = ['admin', 'school_admin']; // ອາດຈະເພີ່ມ 'counselor', 'special_ed_teacher'
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດທີ່ສຸດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ບັນທຶກທັງໝົດ (ຂອງນັກຮຽນຄົນໃດຄົນໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // Context check likely in controller
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດບັນທຶກສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles, // Role ຫຼັກເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງ, ຄູທີ່ກ່ຽວຂ້ອງ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent', 'isTeacherOfStudent'],
            ],
            // ໃຜສາມາດສ້າງບັນທຶກໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_special_needs'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_special_needs'], // ຕົວຢ່າງ permission
                'exceptions' => [], // ບໍ່ຄວນໃຫ້ ນຮ/ຜປຄ ແກ້ໄຂໂດຍກົງ
            ],
            // ໃຜສາມາດລຶບບັນທຶກໄດ້?
            'delete' => [
                'roles' => $managementRoles, // ອະນຸຍາດໃຫ້ຜູ້ຈັດການລຶບໄດ້
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Special Need record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentSpecialNeed || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Special Need record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentSpecialNeed || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດ StudentSpecialNeed model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $student->student_id)->exists() ?? false;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ກ່ຽວຂ້ອງກັບນັກຮຽນຂອງ Special Need record ນີ້ ຫຼື ບໍ່.
     */
    protected function isTeacherOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentSpecialNeed || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ສົມມຸດ StudentSpecialNeed model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        $teacherId = $user->teacher?->getKey();
        if (!$teacherId) return false;

        // ຫາການລົງທະບຽນປັດຈຸບັນຂອງນັກຮຽນ
        $currentYear = AcademicYear::where('is_current', true)->first();
        if (!$currentYear) return false;
        $currentEnrollment = $student->enrollments()
            ->where('academic_year_id', $currentYear->getKey())
            ->first();
        if (!$currentEnrollment || !$currentEnrollment->schoolClass) return false;

        $class = $currentEnrollment->schoolClass;

        // ກວດສອບວ່າເປັນຄູປະຈຳຫ້ອງບໍ່
        if ($class->homeroom_teacher_id === $teacherId) {
            return true;
        }

        // ກວດສອບວ່າສອນວິຊາໃດໜຶ່ງໃນຫ້ອງນີ້ບໍ່
        return ClassSubject::where('class_id', $class->getKey())
            ->where('teacher_id', $teacherId)
            ->exists();
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
