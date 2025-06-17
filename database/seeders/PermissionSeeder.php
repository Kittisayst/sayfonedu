<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ສິດທິດ້ານຜູ້ໃຊ້
            ['permission_name' => 'view_users', 'description' => 'ສາມາດເບິ່ງລາຍຊື່ຜູ້ໃຊ້ລະບົບທັງໝົດ'],
            ['permission_name' => 'create_users', 'description' => 'ສາມາດສ້າງຜູ້ໃຊ້ລະບົບໃໝ່'],
            ['permission_name' => 'edit_users', 'description' => 'ສາມາດແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້ລະບົບ'],
            ['permission_name' => 'delete_users', 'description' => 'ສາມາດລຶບຜູ້ໃຊ້ລະບົບ'],

            // ສິດທິດ້ານນັກຮຽນ
            ['permission_name' => 'view_students', 'description' => 'ສາມາດເບິ່ງຂໍ້ມູນນັກຮຽນ'],
            ['permission_name' => 'create_students', 'description' => 'ສາມາດເພີ່ມນັກຮຽນໃໝ່'],
            ['permission_name' => 'edit_students', 'description' => 'ສາມາດແກ້ໄຂຂໍ້ມູນນັກຮຽນ'],
            ['permission_name' => 'delete_students', 'description' => 'ສາມາດລຶບຂໍ້ມູນນັກຮຽນ'],

            // ສິດທິດ້ານຄູສອນ
            ['permission_name' => 'view_teachers', 'description' => 'ສາມາດເບິ່ງຂໍ້ມູນຄູສອນ'],
            ['permission_name' => 'create_teachers', 'description' => 'ສາມາດເພີ່ມຄູສອນໃໝ່'],
            ['permission_name' => 'edit_teachers', 'description' => 'ສາມາດແກ້ໄຂຂໍ້ມູນຄູສອນ'],
            ['permission_name' => 'delete_teachers', 'description' => 'ສາມາດລຶບຂໍ້ມູນຄູສອນ'],

            // ສິດທິດ້ານຫ້ອງຮຽນແລະວິຊາຮຽນ
            ['permission_name' => 'view_classes', 'description' => 'ສາມາດເບິ່ງຂໍ້ມູນຫ້ອງຮຽນ'],
            ['permission_name' => 'manage_classes', 'description' => 'ສາມາດຈັດການຫ້ອງຮຽນ'],
            ['permission_name' => 'view_subjects', 'description' => 'ສາມາດເບິ່ງຂໍ້ມູນວິຊາຮຽນ'],
            ['permission_name' => 'manage_subjects', 'description' => 'ສາມາດຈັດການວິຊາຮຽນ'],

            // ສິດທິດ້ານຄະແນນ
            ['permission_name' => 'view_grades', 'description' => 'ສາມາດເບິ່ງຄະແນນ'],
            ['permission_name' => 'manage_grades', 'description' => 'ສາມາດຈັດການຄະແນນ'],

            // ສິດທິດ້ານການເງິນ
            ['permission_name' => 'view_fees', 'description' => 'ສາມາດເບິ່ງຄ່າທຳນຽມແລະການຊຳລະເງິນ'],
            ['permission_name' => 'manage_fees', 'description' => 'ສາມາດຈັດການຄ່າທຳນຽມແລະການຊຳລະເງິນ'],
            ['permission_name' => 'view_finances', 'description' => 'ສາມາດເບິ່ງລາຍງານການເງິນ'],
            ['permission_name' => 'manage_finances', 'description' => 'ສາມາດຈັດການລາຍຮັບ-ລາຍຈ່າຍ'],

            // ສິດທິດ້ານລະບົບ
            ['permission_name' => 'view_settings', 'description' => 'ສາມາດເບິ່ງການຕັ້ງຄ່າລະບົບ'],
            ['permission_name' => 'manage_settings', 'description' => 'ສາມາດຈັດການຕັ້ງຄ່າລະບົບ'],
            ['permission_name' => 'view_logs', 'description' => 'ສາມາດເບິ່ງ logs ຂອງລະບົບ'],
            ['permission_name' => 'manage_backups', 'description' => 'ສາມາດຈັດການ backups'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
