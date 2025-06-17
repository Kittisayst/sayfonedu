<?php

namespace App\Policies;

use App\Models\ReportTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ReportTemplatePolicy extends AppPolicy
{
    /**
     * Override method ນີ້ ເພື່ອກຳນົດຄ່າ Roles, Permissions, ແລະ Exceptions ສຳລັບ ReportTemplate Policy.
     */
    protected function getRolesAndExceptionsConfig(): array
    {
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍຊື່ແມ່ແບບໄດ້
        $viewListRoles = ['admin', 'school_admin', 'teacher']; // ອະນຸຍາດໃຫ້ຄູເບິ່ງລາຍຊື່ແມ່ແບບໄດ້
        // ກຳນົດ Roles ທີ່ສາມາດເບິ່ງລາຍລະອຽດ/ເນື້ອໃນແມ່ແບບໄດ້
        $viewDetailRoles = ['admin', 'school_admin']; // ຈຳກັດການເບິ່ງເນື້ອໃນ, ຍົກເວັ້ນຜູ້ສ້າງ
        // ກຳນົດ Roles ທີ່ສາມາດຈັດການ (ສ້າງ/ແກ້ໄຂ/ລຶບ) ແມ່ແບບໄດ້
        $managementRoles = ['admin', 'school_admin'];
        // ກຳນົດ Roles ທີ່ສາມາດລຶບໄດ້
        $deletionRoles = ['admin', 'school_admin'];

        return [
            // ໃຜສາມາດເບິ່ງລາຍຊື່ແມ່ແບບທັງໝົດໄດ້?
            'viewAny' => [
                'roles' => $viewListRoles,
                'permissions' => [],
                'exceptions' => [],
            ],
            // ໃຜສາມາດເບິ່ງລາຍລະອຽດແມ່ແບບສະເພາະອັນໄດ້? (ລວມເຖິງ content)
            'view' => [
                'roles' => $viewDetailRoles, // ຈຳກັດການເບິ່ງ content
                'permissions' => [],
                // ຍົກເວັ້ນ: ຜູ້ສ້າງແມ່ແບບເອງ
                'exceptions' => ['isCreator'],
            ],
            // ໃຜສາມາດສ້າງແມ່ແບບໃໝ່ໄດ້?
            'create' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_report_templates'], // ຕົວຢ່າງ permission
                'exceptions' => [],
            ],
            // ໃຜສາມາດແກ້ໄຂແມ່ແບບໄດ້?
            'update' => [
                'roles' => $managementRoles,
                'permissions' => ['manage_report_templates'], // ຕົວຢ່າງ permission
                // ຍົກເວັ້ນ: ຜູ້ສ້າງແມ່ແບບເອງ
                'exceptions' => ['isCreator'],
            ],
            // ໃຜສາມາດລຶບແມ່ແບບໄດ້?
            'delete' => [
                'roles' => $deletionRoles,
                'permissions' => [],
                // FK constraint ໃນ GeneratedReports ຈະປ້ອງກັນການລຶບຖ້າມີການໃຊ້ງານຢູ່
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
     * Exception: ກວດສອບວ່າຜູ້ໃຊ້ທີ່ login ຢູ່ ເປັນຜູ້ສ້າງ Report Template record ນີ້ ຫຼື ບໍ່.
     */
    protected function isCreator(User $user, Model $model): bool
    {
        if (!$model instanceof ReportTemplate) {
            return false;
        }
        // ກວດສອບວ່າ user_id ກົງກັບ created_by ຫຼືບໍ່
        return $user->user_id === $model->created_by;
    }


    // Standard methods (viewAny, view, create, update, delete, etc.)
    // ຖືກສືບທອດມາຈາກ AppPolicy ແລະ ຈະເອີ້ນໃຊ້ authorize() ທີ່ໃຊ້ config ຂ້າງເທິງນີ້.
}
