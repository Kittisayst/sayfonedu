<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ຂັ້ນຕອນ 1: ສ້າງພື້ນຖານສິດທິ (Permissions)
        $permissions = [
            // ສິດທິພື້ນຖານໃນການຈັດການຂໍ້ມູນ (CRUD)
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',

            // ສິດທິພິເສດອື່ນໆ
            'manage_settings',
            'manage_roles',
            'manage_users',
            'manage_backups',
            'access_reports',
            'export_data',
            'import_data',

            // ສິດທິດ້ານການເງິນ
            'manage_finance',
            'manage_payments',
            'approve_discounts',

            // ສິດທິດ້ານການຮຽນການສອນ
            'manage_grades',
            'manage_attendance',
            'manage_schedules',

            // ສິດທິດ້ານການຈັດການລະບົບ
            'manage_school_store',
            'manage_digital_library',
            'manage_extracurricular'
        ];

        // ສ້າງລາຍການ base models ທີ່ຈະມີການ apply permission
        $models = [
            'user',
            'role',
            'permission',
            'student',
            'teacher',
            'parent',
            'academic_year',
            'school_class',
            'subject',
            'attendance',
            'grade',
            'fee',
            'payment',
            'discount',
            'announcement',
            'report',
            'request',
            'school_store_item',
            'digital_library_resource',
            'extracurricular_activity'
        ];

        // ສ້າງ permissions ທັງໝົດ
        $allPermissions = [];

        // ສ້າງ CRUD permissions ສຳລັບແຕ່ລະ model
        foreach ($models as $model) {
            foreach (['view_any', 'view', 'create', 'update', 'delete', 'restore', 'force_delete'] as $action) {
                $allPermissions[] = "{$action}_{$model}";
            }
        }

        // ເພີ່ມສິດທິພິເສດອື່ນໆ
        $allPermissions = array_merge($allPermissions, $permissions);

        // ສ້າງ permissions ເຂົ້າໃນຖານຂໍ້ມູນ
        foreach (array_unique($allPermissions) as $permission) {
            Permission::create([
                'permission_name' => $permission,
                'description' => 'ສິດທິໃນການ ' . str_replace('_', ' ', $permission),
            ]);
        }

        // ຂັ້ນຕອນ 2: ສ້າງບົດບາດ (Roles)
        $roles = [
            [
                'role_name' => 'admin',
                'description' => 'ຜູ້ບໍລິຫານລະບົບສູງສຸດ ມີສິດທິເຂົ້າເຖິງທຸກຢ່າງ',
            ],
            [
                'role_name' => 'school_admin',
                'description' => 'ຜູ້ບໍລິຫານໂຮງຮຽນ',
            ],
            [
                'role_name' => 'teacher',
                'description' => 'ຄູສອນ',
            ],
            [
                'role_name' => 'finance_staff',
                'description' => 'ພະນັກງານການເງິນ',
            ],
            [
                'role_name' => 'student',
                'description' => 'ນັກຮຽນ',
            ],
            [
                'role_name' => 'parent',
                'description' => 'ຜູ້ປົກຄອງນັກຮຽນ',
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // ຂັ້ນຕອນ 3: ເຊື່ອມໂຍງສິດທິກັບບົດບາດ

        // Admin ມີສິດທິທັງໝົດ
        $adminRole = Role::where('role_name', 'admin')->first();
        $allPermissionIds = Permission::pluck('permission_id')->toArray();
        $adminRole->permissions()->attach($allPermissionIds);

        // School Admin ມີສິດທິຫຼາຍຢ່າງ ແຕ່ບໍ່ໝົດ
        $schoolAdminRole = Role::where('role_name', 'school_admin')->first();
        $schoolAdminPermissions = Permission::whereNotIn('permission_name', [
            'manage_roles',
            'force_delete_user',
            'manage_backups',
            'manage_settings'
        ])->pluck('permission_id')->toArray();
        $schoolAdminRole->permissions()->attach($schoolAdminPermissions);

        // Teacher ມີສິດທິຈຳກັດ
        $teacherRole = Role::where('role_name', 'teacher')->first();
        $teacherPermissions = Permission::whereIn('permission_name', [
            'view_any_student',
            'view_student',
            'view_any_school_class',
            'view_school_class',
            'view_any_subject',
            'view_subject',
            'view_any_academic_year',
            'view_academic_year',
            'view_any_attendance',
            'view_attendance',
            'create_attendance',
            'update_attendance',
            'view_any_grade',
            'view_grade',
            'create_grade',
            'update_grade',
            'view_any_digital_library_resource',
            'view_digital_library_resource',
            'view_any_announcement',
            'view_announcement',
            'create_request',
            'view_request',
        ])->pluck('permission_id')->toArray();
        $teacherRole->permissions()->attach($teacherPermissions);

        // Finance Staff
        $financeRole = Role::where('role_name', 'finance_staff')->first();
        $financePermissions = Permission::whereIn('permission_name', [
            'view_any_student',
            'view_student',
            'view_any_fee',
            'view_fee',
            'create_fee',
            'update_fee',
            'view_any_payment',
            'view_payment',
            'create_payment',
            'update_payment',
            'view_any_discount',
            'view_discount',
            'manage_finance',
            'access_reports',
        ])->pluck('permission_id')->toArray();
        $financeRole->permissions()->attach($financePermissions);

        // Student & Parent ມີສິດທິໜ້ອຍທີ່ສຸດ
        // ...ສາມາດສືບຕໍ່ເພີ່ມສິດທິຕາມຄວາມຕ້ອງການ
    }
}
