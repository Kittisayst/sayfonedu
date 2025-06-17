<?php

namespace Database\Seeders;

use App\Models\Ethnicity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EthnicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ethnicities = [
            [
                'ethnicity_name_lao' => 'ລາວລຸ່ມ',
                'ethnicity_name_en' => 'Lao Loum',
            ],
            [
                'ethnicity_name_lao' => 'ລາວເທິງ',
                'ethnicity_name_en' => 'Lao Theung',
            ],
            [
                'ethnicity_name_lao' => 'ລາວສູງ',
                'ethnicity_name_en' => 'Lao Soung',
            ],
            [
                'ethnicity_name_lao' => 'ມົ້ງ',
                'ethnicity_name_en' => 'Hmong',
            ],
            [
                'ethnicity_name_lao' => 'ຄະມຸ',
                'ethnicity_name_en' => 'Khmu',
            ],
            [
                'ethnicity_name_lao' => 'ລື້',
                'ethnicity_name_en' => 'Lue',
            ],
            [
                'ethnicity_name_lao' => 'ອາຄາ',
                'ethnicity_name_en' => 'Akha',
            ],
            [
                'ethnicity_name_lao' => 'ໄຕດຳ',
                'ethnicity_name_en' => 'Tai Dam',
            ],
            [
                'ethnicity_name_lao' => 'ໄຕແດງ',
                'ethnicity_name_en' => 'Tai Daeng',
            ],
            [
                'ethnicity_name_lao' => 'ອື່ນໆ',
                'ethnicity_name_en' => 'Other',
            ],
        ];
        
        foreach ($ethnicities as $ethnicity) {
            Ethnicity::create($ethnicity);
        }
    }
}
