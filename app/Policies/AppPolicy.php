<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class AppPolicy
{
    use HandlesAuthorization;

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຜູ້ບໍລິຫານລະບົບຫຼືບໍ່
     */
    protected function isAdmin(User $user): bool
    {
        return $user->role && $user->role->role_name === 'admin';
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຜູ້ບໍລິຫານໂຮງຮຽນຫຼືບໍ່
     */
    protected function isSchoolAdmin(User $user): bool
    {
        return $user->role && $user->role->role_name === 'school_admin';
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຄູສອນຫຼືບໍ່
     */
    protected function isTeacher(User $user): bool
    {
        return $user->role && $user->role->role_name === 'teacher';
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນພະນັກງານການເງິນຫຼືບໍ່
     */
    protected function isFinanceStaff(User $user): bool
    {
        return $user->role && $user->role->role_name === 'finance_staff';
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນນັກຮຽນຫຼືບໍ່
     */
    protected function isStudent(User $user): bool
    {
        return $user->role && $user->role->role_name === 'student';
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຜູ້ປົກຄອງຫຼືບໍ່
     */
    protected function isParent(User $user): bool
    {
        return $user->role && $user->role->role_name === 'parent';
    }

    /**
     * ກວດສອບສິດທິຜູ້ໃຊ້ໃນການເຮັດການກະທຳໃດໜຶ່ງ
     */
    protected function userHasPermission(User $user, string $permission): bool
    {
        if ($this->isAdmin($user)) {
            return true; // ຜູ້ບໍລິຫານລະບົບມີສິດທິທັງໝົດ
        }

        if (!$user->role) {
            return false; // ຜູ້ໃຊ້ບໍ່ມີບົດບາດ
        }

        // ກວດສອບວ່າບົດບາດຂອງຜູ້ໃຊ້ມີສິດທິນີ້ຫຼືບໍ່
        return $user->role->permissions()->where('permission_name', $permission)->exists();
    }

    /**
     * ກວດສອບວ່າຄູສອນເປັນຄູປະຈຳຫ້ອງຂອງຫ້ອງນີ້ຫຼືບໍ່
     */
    protected function isHomeroomTeacher(User $user, $classId): bool
    {
        if (!$this->isTeacher($user) || !$user->teacher) {
            return false;
        }

        // ກວດສອບວ່າຄູສອນຄົນນີ້ ເປັນຄູປະຈຳຫ້ອງຂອງຫ້ອງນີ້ຫຼືບໍ່
        return \App\Models\SchoolClass::where('class_id', $classId)
            ->where('homeroom_teacher_id', $user->teacher->teacher_id)
            ->exists();
    }

    /**
     * ກວດສອບວ່າຄູສອນສອນວິຊານີ້ໃນຫ້ອງນີ້ຫຼືບໍ່
     */
    protected function teachesSubjectInClass(User $user, $classId, $subjectId): bool
    {
        if (!$this->isTeacher($user) || !$user->teacher) {
            return false;
        }

        // ກວດສອບວ່າຄູສອນຄົນນີ້ ສອນວິຊານີ້ໃນຫ້ອງນີ້ຫຼືບໍ່
        return \App\Models\ClassSubject::where('class_id', $classId)
            ->where('subject_id', $subjectId)
            ->where('teacher_id', $user->teacher->teacher_id)
            ->exists();
    }

    /**
     * ກວດສອບວ່າຜູ້ປົກຄອງເປັນຜູ້ປົກຄອງຂອງນັກຮຽນຄົນນີ້ຫຼືບໍ່
     */
    protected function isParentOfStudent(User $user, $studentId): bool
    {
        if (!$this->isParent($user) || !$user->studentParent) {
            return false;
        }

        // ກວດສອບວ່າຜູ້ປົກຄອງຄົນນີ້ ເປັນຜູ້ປົກຄອງຂອງນັກຮຽນຄົນນີ້ຫຼືບໍ່
        return \App\Models\StudentParent::where('parent_id', $user->studentParent->parent_id)
            ->whereHas('students', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->exists();
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນເຈົ້າຂອງຂໍ້ມູນນີ້ຫຼືບໍ່
     */
    protected function isOwner(User $user, Model $model): bool
    {
        // ຖ້າ model ມີ user_id field
        if (isset($model->user_id)) {
            return $user->user_id === $model->user_id;
        }

        // ຖ້າເປັນນັກຮຽນ ແລະ model ແມ່ນ Student
        if ($this->isStudent($user) && $model instanceof \App\Models\Student) {
            return $user->student && $user->student->student_id === $model->student_id;
        }

        // ຖ້າເປັນຄູສອນ ແລະ model ແມ່ນ Teacher
        if ($this->isTeacher($user) && $model instanceof \App\Models\Teacher) {
            return $user->teacher && $user->teacher->teacher_id === $model->teacher_id;
        }

        // ຖ້າເປັນຜູ້ປົກຄອງ ແລະ model ແມ່ນ StudentParents
        if ($this->isParent($user) && $model instanceof \App\Models\StudentParent) {
            return $user->studentParent && $user->studentParent->parent_id === $model->parent_id;
        }

        return false;
    }

    /**
     * ແມ່ແບບສຳລັບນະໂຍບາຍບົດບາດແລະຂໍ້ຍົກເວັ້ນ
     * ຄວນຖືກຜ່ານທັບຈາກ Policy ລູກ
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ໃຫ້ຄ່າເລີ່ມຕົ້ນ - ໃນ Policy ລູກຄວນຜ່ານທັບ
        return [
            'viewAny' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [], // ຖ້າມີສິດທິສະເພາະສຳລັບ viewAny
                'exceptions' => [], // ເງື່ອນໄຂພິເສດຖ້າມີ
            ],
            'view' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
            'create' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
            'update' => [
                'roles' => ['admin', 'school_admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
            'delete' => [
                'roles' => ['admin'],
                'permissions' => [],
                'exceptions' => [],
            ],
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
     * ເມັດທອດຊ່ວຍເຫຼືອທີ່ຈະກວດສອບການອະນຸຍາດຕາມບົດບາດ, ສິດທິ, ແລະຂໍ້ຍົກເວັ້ນ
     */
    protected function authorize(User $user, string $action, ?Model $model = null): bool
    {
        $config = $this->getRolesAndExceptionsConfig();
        $actionConfig = $config[$action] ?? [
            'roles' => ['admin'],
            'permissions' => [],
            'exceptions' => [],
        ];

        // ກວດສອບບົດບາດທີ່ອະນຸຍາດ
        if (in_array($user->role->role_name, $actionConfig['roles'])) {
            return true;
        }

        // ກວດສອບສິດທິທີ່ອະນຸຍາດ
        foreach ($actionConfig['permissions'] as $permission) {
            if ($this->userHasPermission($user, $permission)) {
                return true;
            }
        }

        // ກວດສອບຂໍ້ຍົກເວັ້ນ (ຖ້າມີ model)
        if ($model && !empty($actionConfig['exceptions'])) {
            foreach ($actionConfig['exceptions'] as $exception) {
                if (is_callable([$this, $exception]) && $this->$exception($user, $model)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດເບິ່ງລາຍການໃດໜຶ່ງໄດ້
     */
    public function viewAny(User $user)
    {
        return $this->authorize($user, 'viewAny');
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດເບິ່ງ model ສະເພາະໄດ້
     */
    public function view(User $user, Model $model)
    {
        return $this->authorize($user, 'view', $model);
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດສ້າງ model ໄດ້
     */
    public function create(User $user)
    {
        return $this->authorize($user, 'create');
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດອັບເດດ model ສະເພາະໄດ້
     */
    public function update(User $user, Model $model)
    {
        return $this->authorize($user, 'update', $model);
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດລຶບ model ສະເພາະໄດ້
     */
    public function delete(User $user, Model $model)
    {
        return $this->authorize($user, 'delete', $model);
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດກູ້ຄືນ model ທີ່ຖືກລຶບໄດ້
     */
    public function restore(User $user, Model $model)
    {
        return $this->authorize($user, 'restore', $model);
    }

    /**
     * ກຳນົດວ່າຜູ້ໃຊ້ສາມາດລຶບ model ຖາວອນໄດ້
     */
    public function forceDelete(User $user, Model $model)
    {
        return $this->authorize($user, 'forceDelete', $model);
    }

    /**
     * ຟັງຊັນທົ່ວໄປເພື່ອໃຫ້ລູກ policy ສາມາດສືບທອດໃຊ້ໄດ້
     */
    protected function denyUnlessAdminOrHasPermission(User $user, string $permission): bool
    {
        return $this->isAdmin($user) || $this->userHasPermission($user, $permission);
    }
}