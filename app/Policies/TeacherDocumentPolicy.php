<?php

namespace App\Policies;

use App\Models\TeacherDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class TeacherDocumentPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ TeacherDocument Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin']; // Teacher ເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງ/ອັບໂຫຼດໄດ້
        $createRoles = ['admin', 'school_admin', 'teacher']; // ອະນຸຍາດໃຫ້ Teacher ອັບໂຫຼດເອກະສານຕົນເອງ
        // ກຳນົດ Roles ທີ່ສາມາດແກ້ໄຂ metadata ໄດ້
        $updateRoles = ['admin', 'school_admin']; // Owner ແກ້ໄຂຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin']; // Owner ລຶບຜ່ານ exception

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ເອກະສານທັງໝົດ (ຂອງຄູຄົນໃດຄົນໜຶ່ງ)? (Filter ໃນ Controller)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [], // ການກວດສອບວ່າເບິ່ງຂອງໃຜແມ່ນເຮັດໃນ Controller/Query
            ],
            // ໃຜສາມາດເບິ່ງ/ດາວໂຫຼດເອກະສານສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຄູເຈົ້າຂອງເອກະສານ
                'exceptions' => ['isOwnerOfRecord'],
            ],
            // ໃຜສາມາດອັບໂຫຼດເອກະສານໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['upload_teacher_documents'], // ຕົວຢ່າງ permission
                // ການກວດສອບວ່າ Teacher ອັບໂຫຼດໃຫ້ຕົນເອງ ແມ່ນເຮັດໃນ Controller/Request
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນເອກະສານໄດ້? (ເຊັ່ນ: ປ່ຽນ type, description)
            'update' => [
                'roles' => $updateRoles, // Admin, School Admin
                'permissions' => ['manage_teacher_documents'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຄູເຈົ້າຂອງເອກະສານ
                'exceptions' => ['isOwnerOfRecord'],
            ],
            // ໃຜສາມາດລຶບເອກະສານໄດ້?
            'delete' => [
                'roles' => $deletionRoles, // Admin, School Admin
                'permissions' => [],
                // ຍົກເວັ້ນ: ຄູເຈົ້າຂອງເອກະສານລຶບເອກະສານຕົນເອງໄດ້
                'exceptions' => ['isOwnerOfRecord'],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຄູເຈົ້າຂອງ Document record ນີ້ ຫຼື ບໍ່.
     */
    protected function isOwnerOfRecord(User $user, Model $model): bool
    {
        if (!$model instanceof TeacherDocument || !$this->isTeacher($user)) { // ໃຊ້ isTeacher ຈາກ AppPolicy
            return false;
        }
        // ສົມມຸດວ່າ User model ມີ relationship 'teacher'
        // ແລະ Teacher model ມີ primary key 'teacher_id'
        return $user->teacher?->teacher_id === $model->teacher_id;
    }

    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
