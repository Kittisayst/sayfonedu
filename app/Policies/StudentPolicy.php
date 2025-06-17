<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy extends AppPolicy
{
    /**
     * ລຶບທັບຄ່າເລີ່ມຕົ້ນຂອງ roles ແລະ ຂໍ້ຍົກເວັ້ນ
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        return [
            'viewAny' => [
                'roles' => ['admin', 'school_admin', 'teacher', 'finance_staff'],
                'permissions' => ['view_any_student'],
                'exceptions' => [],
            ],
            'view' => [
                'roles' => ['admin', 'school_admin', 'teacher', 'finance_staff'],
                'permissions' => ['view_student'],
                'exceptions' => ['isParentOfStudent', 'isOwner'],
            ],
            'create' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['create_student'],
                'exceptions' => [],
            ],
            'update' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['update_student'],
                'exceptions' => [],
            ],
            'delete' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['delete_student'],
                'exceptions' => [],
            ],
            'restore' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => ['restore_student'],
                'exceptions' => [],
            ],
            'forceDelete' => [
                'roles' => ['admin'],
                'permissions' => ['force_delete_student'],
                'exceptions' => [],
            ],
        ];
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດເບິ່ງຂໍ້ມູນສ່ວນຕົວຂອງ Student ໄດ້
     */
    public function viewPersonalInfo(User $user, Student $student)
    {
        // ຜູ້ບໍລິຫານລະບົບ ຫຼື ໂຮງຮຽນສາມາດເບິ່ງຂໍ້ມູນສ່ວນຕົວໄດ້ທັງໝົດ
        if ($this->isAdmin($user) || $this->isSchoolAdmin($user)) {
            return true;
        }

        // ຄູປະຈຳຫ້ອງສາມາດເບິ່ງຂໍ້ມູນສ່ວນຕົວຂອງນັກຮຽນໃນຫ້ອງໄດ້
        $currentEnrollment = $student->currentEnrollment;
        if ($currentEnrollment && $this->isHomeroomTeacher($user, $currentEnrollment->class_id)) {
            return true;
        }

        // ຜູ້ປົກຄອງສາມາດເບິ່ງຂໍ້ມູນສ່ວນຕົວຂອງລູກໄດ້
        if ($this->isParentOfStudent($user, $student->student_id)) {
            return true;
        }

        // ນັກຮຽນສາມາດເບິ່ງຂໍ້ມູນຂອງຕົນເອງໄດ້
        if ($this->isStudent($user) && $user->student && $user->student->student_id === $student->student_id) {
            return true;
        }

        return false;
    }
}
