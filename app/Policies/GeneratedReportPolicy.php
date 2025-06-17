<?php

namespace App\Policies;

use App\Models\GeneratedReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class GeneratedReportPolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ GeneratedReport Policy.
     * ໂດຍທົ່ວໄປ, ການສ້າງ/ແກ້ໄຂ record ນີ້ຈະເຮັດໂດຍລະບົບ, User ຈະມີສິດເບິ່ງ ຫຼື ລຶບ.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍຊື່ລາຍງານທີ່ສ້າງແລ້ວໄດ້
        $viewListRoles = ['admin', 'school_admin', 'finance_staff', 'teacher']; // ປັບຕາມຄວາມເໝາະສົມ
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງ/ດາວໂຫຼດລາຍງານສະເພາະໄດ້
        $viewRecordRoles = ['admin', 'school_admin', 'finance_staff']; // ຜູ້ສ້າງເບິ່ງຜ່ານ exception
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin']; // ຜູ້ສ້າງລຶບຜ່ານ exception
        // ກຳນົດວ່າບໍ່ມີໃຜສ້າງ/ແກ້ໄຂຜ່ານ Policy ນີ້ໂດຍກົງ
        $noOne = [];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ລາຍງານທີ່ສ້າງແລ້ວທັງໝົດໄດ້? (ຄວນ Filter)
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => ['view_reports'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງ/ດາວໂຫຼດລາຍງານສະເພາະອັນໄດ້?
            'view' => [
                'roles' => $viewRecordRoles,
                'permissions' => ['view_reports'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ທີ່ສ້າງລາຍງານນັ້ນ
                'exceptions' => ['isGenerator'],
            ],
            // ໃຜສາມາດສ້າງ record GeneratedReport ໃໝ່ໄດ້? (ລະບົບເປັນຜູ້ສ້າງ)
            'create' => [
                'roles' => $noOne,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂ record GeneratedReport ໄດ້? (ບໍ່ຄວນມີ)
            'update' => [
                'roles' => $noOne,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດລຶບ record/ໄຟລ໌ລາຍງານທີ່ສ້າງແລ້ວໄດ້?
            'delete' => [
                'roles' => $deletionRoles,
                'permissions' => ['manage_reports'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ທີ່ສ້າງລາຍງານນັ້ນ
                'exceptions' => ['isGenerator'],
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ສ້າງ Report record ນີ້ ຫຼື ບໍ່.
     */
    protected function isGenerator(User $user, Model $model): bool
    {
        if (!$model instanceof GeneratedReport) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ generated_by ຫຼືບໍ່
        return $user->user_id === $model->generated_by;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
