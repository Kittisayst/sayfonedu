<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\StudentEmergencyContact;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentEmergencyContactPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ StudentEmergencyContact Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher']; // Student/Parent ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງບັນທຶກໄດ້
        $createRoles = ['admin', 'school_admin', 'student', 'parent']; // ອະນຸຍາດໃຫ້ ນຮ/ຜປຄ ເພີ່ມຂໍ້ມູນເອງ
        // ກຳນົດ Roles ທີ່ສາມາດແກ້ໄຂໄດ້
        $updateRoles = ['admin', 'school_admin']; // Owner/Guardian ແກ້ໄຂຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin']; // Owner/Guardian ລຶບຜ່ານ exception

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນທັງໝົດ (ຂອງນັກຮຽນຄົນໃດຄົນໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // Context check likely in controller
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດຜູ້ຕິດຕໍ່ສຸກເສີນສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງ, ຄູທີ່ກ່ຽວຂ້ອງ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent', 'isTeacherOfStudent'],
            ],
            // ໃຜສາມາດສ້າງບັນທຶກຜູ້ຕິດຕໍ່ສຸກເສີນໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['manage_student_profile'], // ຕົວຢ່າງ permission
                // ການກວດສອບວ່າ ນຮ/ຜປຄ ເພີ່ມໃຫ້ຖືກຄົນ ແມ່ນເຮັດໃນ Controller/Request
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກຜູ້ຕິດຕໍ່ສຸກເສີນໄດ້?
            'update' => [
                'roles' => $updateRoles, // Admin, School Admin
                'permissions' => ['manage_student_profile'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ ຫຼື ຜູ້ປົກຄອງ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
            ],
            // ໃຜສາມາດລຶບບັນທຶກຜູ້ຕິດຕໍ່ສຸກເສີນໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ ຫຼື ຜູ້ປົກຄອງ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ Emergency Contact record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEmergencyContact || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ Emergency Contact record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEmergencyContact || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດ StudentEmergencyContact model ມີ relationship 'student'
        $student = $model->student;
        if (!$student) return false;

        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model (StudentParents)
        // ແລະ Guardian->students relationship ມີຢູ່
        // ໃຊ້ຊື່ Model ຜູ້ປົກຄອງທີ່ເຮົາຕົກລົງກັນຄື StudentParents
        return $user->guardian?->students()->where('students.student_id', $student->student_id)->exists() ?? false;
    }

    /**
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ກ່ຽວຂ້ອງກັບນັກຮຽນຂອງ Emergency Contact record ນີ້ ຫຼື ບໍ່.
     */
    protected function isTeacherOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof StudentEmergencyContact || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ສົມມຸດ StudentEmergencyContact model ມີ relationship 'student'
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
