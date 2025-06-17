<?php

namespace App\Policies;

use App\Models\DigitalLibraryResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DigitalLibraryResourcePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ DigitalLibraryResource Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້ (ຜູ້ໃຊ້ທົ່ວໄປສ່ວນໃຫຍ່)
        $viewListRoles = ['admin', 'school_admin', 'teacher', 'student', 'parent', 'finance_staff', 'librarian']; // ສົມມຸດວ່າມີ Role 'librarian'
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ຊັບພະຍາກອນໄດ້
        $managementRoles = ['admin', 'school_admin', 'librarian'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin', 'librarian'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ຊັບພະຍາກອນທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດຊັບພະຍາກອນສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewListRoles, // ອະນຸຍາດໃຫ້ສ່ວນໃຫຍ່ເບິ່ງໄດ້
                'permissions' => [],
                'exceptions' => [], // ການຄວບຄຸມການ download/print ອາດຈະແຍກຕ່າງຫາກ
            ],
            // ໃຜສາມາດສ້າງ (ເພີ່ມ) ຊັບພະຍາກອນໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_library'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂຂໍ້ມູນຊັບພະຍາກອນໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_library'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ທີ່ເພີ່ມເຂົ້າມາເດີມ
                'exceptions' => ['isAdder'],
            ],
            // ໃຜສາມາດລຶບຊັບພະຍາກອນໄດ້?
            'delete' => [
                'roles' => $deletionRoles,
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ເພີ່ມ Resource record ນີ້ ຫຼື ບໍ່.
     */
    protected function isAdder(User $user, Model $model): bool
    {
        if (!$model instanceof DigitalLibraryResource) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ added_by ຫຼືບໍ່
        return $user->user_id === $model->added_by;
    }

    /**
     * Helper method: ກວດສອບວ່າຜູ້ໃຊ້ເປັນ Librarian ຫຼື ບໍ່ (ຕົວຢ່າງ).
     * (ຫຼື ຈະເອົາໄປໄວ້ໃນ AppPolicy ຖ້າໃຊ້ຫຼາຍບ່ອນ)
     */
    protected function isLibrarian(User $user): bool
    {
        // ປັບຕາມການກວດສອບ Role ຕົວຈິງຂອງທ່ານ
        return $user->role && $user->role->role_name === 'librarian';
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
