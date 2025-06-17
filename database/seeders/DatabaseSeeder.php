<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            
            // ຂໍ້ມູນພື້ນຖານທາງພູມສາດ
            ProvinceSeeder::class,
            DistrictSeeder::class,
            VillageSeeder::class,
            
            // ຂໍ້ມູນພື້ນຖານດ້ານຊົນເຜົ່າ, ສາສະໜາ, ສັນຊາດ
            NationalitySeeder::class,
            ReligionSeeder::class,
            EthnicitySeeder::class,
            
            // ຂໍ້ມູນພື້ນຖານດ້ານການສຶກສາ
            AcademicYearSeeder::class,
            SubjectSeeder::class,
            
            // ຂໍ້ມູນພື້ນຖານດ້ານການເງິນ
            FeeTypeSeeder::class,
            DiscountSeeder::class,
        ]);
    }
}
