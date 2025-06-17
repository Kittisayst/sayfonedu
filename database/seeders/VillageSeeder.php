<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // ດຶງຂໍ້ມູນເມືອງຈັນທະບູລີ
         $chanthabouly = District::where('district_name_lao', 'ຈັນທະບູລີ')->first();
        
         // ບ້ານໃນເມືອງຈັນທະບູລີ (ຕົວຢ່າງ)
         $chanthabouly_villages = [
             [
                 'village_name_lao' => 'ວັດຈັນ',
                 'village_name_en' => 'Watchan',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ສີຄຳເມືອງ',
                 'village_name_en' => 'Sikhammuang',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ອົງຄ໌',
                 'village_name_en' => 'Ong',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ຕະຫຼາດເຊົ້າ',
                 'village_name_en' => 'Talat Sao',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ຮ່ອມ 3',
                 'village_name_en' => 'Hom 3',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ຮ່ອມ 5',
                 'village_name_en' => 'Hom 5',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ບ້ານຫາຍໂສກ',
                 'village_name_en' => 'Haisok',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ບ້ານຜາໄຊ',
                 'village_name_en' => 'Phaxay',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ບ້ານໄຮ່',
                 'village_name_en' => 'Ban Hai',
                 'district_id' => $chanthabouly->district_id,
             ],
             [
                 'village_name_lao' => 'ບ້ານຫັດສະດີ',
                 'village_name_en' => 'Hatsady',
                 'district_id' => $chanthabouly->district_id,
             ],
         ];
         
         foreach ($chanthabouly_villages as $village) {
             Village::create($village);
         }
         
         // ທ່ານສາມາດເພີ່ມຂໍ້ມູນບ້ານຂອງເມືອງອື່ນໆ ໄດ້ຕາມຕ້ອງການ
         // ໂດຍໃຊ້ວິທີການດຽວກັນ
    }
}
