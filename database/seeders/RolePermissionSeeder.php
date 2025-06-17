<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ສິດທິສຳລັບແຕ່ລະບົດບາດ
        $rolePermissions = [
            'admin' => [
                // Admin ມີສິດທິທຸກຢ່າງ
                'view_users', 'create_users', 'edit_users', 'delete_users',
                'view_students', 'create_students', 'edit_students', 'delete_students',
                'view_teachers', 'create_teachers', 'edit_teachers', 'delete_teachers',
                'view_classes', 'manage_classes', 'view_subjects', 'manage_subjects',
                'view_grades', 'manage_grades', 'view_fees', 'manage_fees',
                'view_finances', 'manage_finances', 'view_settings', 'manage_settings',
                'view_logs', 'manage_backups'
            ],
            
            'teacher' => [
                // ຄູສອນມີສິດທິບາງສ່ວນ
                'view_students', 'view_teachers',
                'view_classes', 'view_subjects',
                'view_grades', 'manage_grades',
                'view_fees'
            ],
            
            'student' => [
                // ນັກຮຽນມີສິດເບິ່ງຂໍ້ມູນຕົນເອງເທົ່ານັ້ນ
                'view_grades',
                'view_fees'
            ],
            
            'parent' => [
                // ຜູ້ປົກຄອງມີສິດເບິ່ງຂໍ້ມູນລູກຫຼານເທົ່ານັ້ນ
                'view_grades',
                'view_fees'
            ],
            
            'staff' => [
                // ພະນັກງານມີສິດຈັດການບາງສ່ວນ
                'view_students', 'create_students', 'edit_students',
                'view_teachers',
                'view_classes', 'view_subjects',
                'view_fees', 'manage_fees',
                'view_finances'
            ]
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('role_name', $roleName)->first();
            
            if ($role) {
                $permissionIds = Permission::whereIn('permission_name', $permissions)
                    ->pluck('permission_id')
                    ->toArray();
                    
                $role->permissions()->sync($permissionIds);
            }
        }
    }
}
