<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ExpensePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ Expense Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງຂໍ້ມູນໄດ້
        $viewRoles = ['admin', 'school_admin', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດສ້າງລາຍຈ່າຍໄດ້ (ອາດຈະກວ້າງກວ່າ)
        $createRoles = ['admin', 'school_admin', 'finance_staff', 'teacher']; // ຕົວຢ່າງ: ອະນຸຍາດໃຫ້ຄູສ້າງໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດອະນຸມັດ ຫຼື ແກ້ໄຂຫຼັງອະນຸມັດ
        $approvalManagementRoles = ['admin', 'school_admin', 'finance_staff'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້ (ຈຳກັດ)
        $deletionRoles = ['admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ລາຍຈ່າຍທັງໝົດໄດ້? (ຄວນ Filter)
            'viewAny' => [
                'roles' => $viewRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດລາຍຈ່າຍສະເພາະ record ໄດ້?
            'view' => [
                'roles' => $viewRoles, // Admin, School Admin, Finance ເບິ່ງໄດ້
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ສ້າງ ຫຼື ຜູ້ອະນຸມັດລາຍການນັ້ນ
                'exceptions' => ['isCreator', 'isApprover'],
            ],
            // ໃຜສາມາດສ້າງລາຍການລາຍຈ່າຍໃໝ່ໄດ້?
            'create' => [
                'roles' => $createRoles,
                'permissions' => ['create_expenses'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂລາຍການລາຍຈ່າຍໄດ້?
            'update' => [
                'roles' => $approvalManagementRoles, // Admin, School Admin, Finance ແກ້ໄຂໄດ້
                'permissions' => ['manage_expenses'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ສ້າງສາມາດແກ້ໄຂໄດ້ (ອາດຈະມີເງື່ອນໄຂເພີ່ມເຕີມ ເຊັ່ນ: ກ່ອນ approve)
                'exceptions' => ['isCreator'],
            ],
            // ໃຜສາມາດລຶບລາຍການລາຍຈ່າຍໄດ້?
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
            // Custom Action: ສິດທິໃນການອະນຸມັດລາຍຈ່າຍ
            'approve' => [
                'roles' => $approvalManagementRoles, // Admin, School Admin, Finance ອະນຸມັດໄດ້
                'permissions' => ['approve_expenses'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
        ];
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ສ້າງ Expense record ນີ້ ຫຼື ບໍ່.
     */
    protected function isCreator(User $user, Model $model): bool
    {
        if (!$model instanceof Expense) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ created_by ຫຼືບໍ່
        return $user->user_id === $model->created_by;
    }

    /**
     * Exception method: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ອະນຸມັດ Expense record ນີ້ ຫຼື ບໍ່.
     */
    protected function isApprover(User $user, Model $model): bool
    {
        if (!$model instanceof Expense) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ approved_by ຫຼືບໍ່ (ຖ້າ approved_by ບໍ່ແມ່ນ NULL)
        return !is_null($model->approved_by) && $user->user_id === $model->approved_by;
    }

    /**
     * Custom Policy Method: ກວດສອບວ່າຜູ້ໃຊ້ສາມາດອະນຸມັດລາຍຈ່າຍນີ້ໄດ້ບໍ່.
     * (ອາດຈະເພີ່ມເງື່ອນໄຂເຊັ່ນ: ຫ້າມອະນຸມັດລາຍຈ່າຍທີ່ຕົນເອງສ້າງ)
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Expense  $expense
     * @return bool
     */
    public function approve(User $user, Expense $expense): bool
    {
        // ກວດສອບເບື້ອງຕົ້ນຜ່ານ authorize helper ໂດຍໃຊ້ config 'approve'
        if (!$this->authorize($user, 'approve', $expense)) {
            return false;
        }

        // ເພີ່ມເງື່ອນໄຂເພີ່ມເຕີມໄດ້ຢູ່ບ່ອນນີ້ ຕົວຢ່າງ:
        // ຫ້າມອະນຸມັດລາຍຈ່າຍທີ່ຕົນເອງເປັນຄົນສ້າງ
        // if ($user->user_id === $expense->created_by) {
        //     return false;
        // }

        // ຫ້າມອະນຸມັດລາຍຈ່າຍທີ່ຖືກອະນຸມັດແລ້ວ
        // if (!is_null($expense->approved_by)) {
        //     return false;
        // }

        return true; // ຜ່ານທຸກເງື່ອນໄຂ
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
