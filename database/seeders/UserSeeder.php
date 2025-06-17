<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // ສ້າງຜູ້ໃຊ້ admin ຕົວຢ່າງ
         $adminRole = Role::where('role_name', 'admin')->first();
        
         User::create([
             'username' => 'admin',
             'email' => 'admin@sayfone.la',
             'password' => Hash::make('password123'),
             'phone' => '02055555555',
             'role_id' => $adminRole->role_id,
             'status' => 'active',
         ]);
         
         // ສ້າງຜູ້ໃຊ້ school_admin ຕົວຢ່າງ
         $schoolAdminRole = Role::where('role_name', 'school_admin')->first();
         
         User::create([
             'username' => 'school_admin',
             'email' => 'schooladmin@sayfone.la',
             'password' => Hash::make('password123'),
             'phone' => '02066666666',
             'role_id' => $schoolAdminRole->role_id,
             'status' => 'active',
         ]);
         
         // ສ້າງຜູ້ໃຊ້ finance_staff ຕົວຢ່າງ
         $financeRole = Role::where('role_name', 'finance_staff')->first();
         
         User::create([
             'username' => 'finance',
             'email' => 'finance@sayfone.la',
             'password' => Hash::make('password123'),
             'phone' => '02077777777',
             'role_id' => $financeRole->role_id,
             'status' => 'active',
         ]);
    }
}
