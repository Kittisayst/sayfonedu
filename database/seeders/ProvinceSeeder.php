<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            [
                'province_name_lao' => 'ນະຄອນຫຼວງວຽງຈັນ',
                'province_name_en' => 'Vientiane Capital',
            ],
            [
                'province_name_lao' => 'ຜົ້ງສາລີ',
                'province_name_en' => 'Phongsaly',
            ],
            [
                'province_name_lao' => 'ຫຼວງນ້ຳທາ',
                'province_name_en' => 'Luang Namtha',
            ],
            [
                'province_name_lao' => 'ອຸດົມໄຊ',
                'province_name_en' => 'Oudomxay',
            ],
            [
                'province_name_lao' => 'ບໍ່ແກ້ວ',
                'province_name_en' => 'Bokeo',
            ],
            [
                'province_name_lao' => 'ຫຼວງພະບາງ',
                'province_name_en' => 'Luang Prabang',
            ],
            [
                'province_name_lao' => 'ຫົວພັນ',
                'province_name_en' => 'Houaphanh',
            ],
            [
                'province_name_lao' => 'ໄຊຍະບູລີ',
                'province_name_en' => 'Xayaboury',
            ],
            [
                'province_name_lao' => 'ຊຽງຂວາງ',
                'province_name_en' => 'Xiengkhouang',
            ],
            [
                'province_name_lao' => 'ວຽງຈັນ',
                'province_name_en' => 'Vientiane Province',
            ],
            [
                'province_name_lao' => 'ບໍລິຄຳໄຊ',
                'province_name_en' => 'Borikhamxay',
            ],
            [
                'province_name_lao' => 'ຄຳມ່ວນ',
                'province_name_en' => 'Khammouane',
            ],
            [
                'province_name_lao' => 'ສະຫວັນນະເຂດ',
                'province_name_en' => 'Savannakhet',
            ],
            [
                'province_name_lao' => 'ສາລະວັນ',
                'province_name_en' => 'Salavan',
            ],
            [
                'province_name_lao' => 'ເຊກອງ',
                'province_name_en' => 'Sekong',
            ],
            [
                'province_name_lao' => 'ຈຳປາສັກ',
                'province_name_en' => 'Champasak',
            ],
            [
                'province_name_lao' => 'ອັດຕະປື',
                'province_name_en' => 'Attapeu',
            ],
            [
                'province_name_lao' => 'ໄຊສົມບູນ',
                'province_name_en' => 'Xaysomboun',
            ],
        ];
        
        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
