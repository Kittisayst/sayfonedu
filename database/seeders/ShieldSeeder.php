<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ShieldSeeder extends Seeder
{
    public function run()
    {
        // ສ້າງບົດບາດ
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $teacher = Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        $student = Role::create(['name' => 'student', 'guard_name' => 'web']);
        $parent = Role::create(['name' => 'parent', 'guard_name' => 'web']);

        // ສ້າງສິດທິພື້ນຖານ
        $permissions = [
            'view_any_students',
            'view_students',
            'create_students',
            'update_students',
            'delete_students',
            'delete_any_students',
            'view_any_teachers',
            'view_teachers',
            'create_teachers',
            'update_teachers',
            'delete_teachers',
            'delete_any_teachers',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // ກຳນົດສິດທິໃຫ້ບົດບາດ
        $admin->givePermissionTo($permissions);
        $teacher->givePermissionTo([
            'view_any_students',
            'view_students',
            'view_any_teachers',
            'view_teachers',
        ]);
    }
}