<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\ClassSubject;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AttendancePolicy extends AppPolicy
{
     /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Attendance Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ) ໄດ້
        $managementRoles = ['admin', 'school_admin', 'teacher'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ການຂາດ-ມາທັງໝົດໄດ້? (ຄວນ Filter ໃນ Controller/Resource)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດການຂາດ-ມາສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $managementRoles, // Admin, School Admin, Teacher ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ນັກຮຽນເຈົ້າຂອງ, ຜູ້ປົກຄອງຂອງນັກຮຽນ
                'exceptions' => ['isOwnerOfRecord', 'isGuardianOfStudent'],
                // isTeacherOfAttendanceRecord ກວດສອບໃນ role 'teacher' ແລ້ວ (ຫຼື ອາດຈະເພີ່ມໃສ່ exception)
            ],
            // ໃຜສາມາດສ້າງບັນທຶກການຂາດ-ມາໃໝ່ໄດ້?
            'create' => [
                // ກວດສອບ Role ກ່ອນ (Admin, School Admin, Teacher)
                // ການກວດສອບວ່າ Teacher ສາມາດບັນທຶກໃຫ້ຫ້ອງ/ວິຊາສະເພາະໃດໜຶ່ງໄດ້ບໍ່ ຄວນເຮັດໃນ Controller/Request
                'roles' => $managementRoles,
                'permissions' => ['record_attendance'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂບັນທຶກການຂາດ-ມາໄດ້?
            'update' => [
                'roles' => $managementRoles, // Admin, School Admin, Teacher ແກ້ໄຂໄດ້
                'permissions' => ['edit_attendance'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ອາດຈະໃຫ້ສະເພາະຜູ້ບັນທຶກເດີມແກ້ໄຂໄດ້ (ພາຍໃນເວລາທີ່ກຳນົດ - ກວດໃນ Controller/Request)
                'exceptions' => ['isRecorder'],
            ],
            // ໃຜສາມາດລຶບບັນທຶກການຂາດ-ມາໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin ລຶບໄດ້
                'permissions' => [],
                'exceptions' => [],
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
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນນັກຮຽນເຈົ້າຂອງ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof Attendance || !$this->isStudent($user)) { // ໃຊ້ isStudent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'student'
        return $user->student?->student_id === $model->student_id;
    }

     /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນໃນ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGuardianOfStudent(User $user, Model $model): bool
    {
        if (!$model instanceof Attendance || !$this->isParent($user)) { // ໃຊ້ isParent ຈາກ AppPolicy
             return false;
        }
        // ສົມມຸດວ່າ User->guardian relationship ສົ່ງຄືນ Guardian model
        // ແລະ Guardian->students relationship ມີຢູ່
        return $user->guardian?->students()->where('students.student_id', $model->student_id)->exists() ?? false;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູທີ່ກ່ຽວຂ້ອງກັບ record ນີ້ ຫຼື ບໍ່
     * (ເຊັ່ນ: ຄູສອນວິຊານັ້ນໃນຫ້ອງນັ້ນ ຫຼື ຄູປະຈຳຫ້ອງ).
     */
    protected function isTeacherOfAttendanceRecord(User $user, Model $model): bool
    {
        if (!$model instanceof Attendance || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }

        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        $teacherId = $user->teacher?->getKey();
        if (!$teacherId) return false;

        // ກໍລະນີບັນທຶກຕາມວິຊາ
        if ($model->subject_id) {
            // ກວດສອບວ່າຄູຄົນນີ້ສອນວິຊານີ້ໃນຫ້ອງນີ້ບໍ່ (ຜ່ານ ClassSubject)
            return ClassSubject::where('class_id', $model->class_id)
                               ->where('subject_id', $model->subject_id)
                               ->where('teacher_id', $teacherId)
                               ->exists();
        }
        // ກໍລະນີບັນທຶກລາຍວັນ (subject_id is NULL)
        else {
            // ກວດສອບວ່າເປັນຄູປະຈຳຫ້ອງບໍ່
            // ສົມມຸດ Attendance model ມີ relationship 'schoolClass'
             $class = $model->schoolClass;
             if (!$class) return false;
             return $class->homeroom_teacher_id === $teacherId;
        }
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ບັນທຶກ record ນີ້ ຫຼື ບໍ່.
     */
    protected function isRecorder(User $user, Model $model): bool
    {
        if (!$model instanceof Attendance) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ recorded_by ຫຼືບໍ່
        return !is_null($model->recorded_by) && $user->user_id === $model->recorded_by;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
