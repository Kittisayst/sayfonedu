<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            [
                'nationality_name_lao' => 'ລາວ',
                'nationality_name_en' => 'Lao',
            ],
            [
                'nationality_name_lao' => 'ໄທ',
                'nationality_name_en' => 'Thai',
            ],
            [
                'nationality_name_lao' => 'ຫວຽດນາມ',
                'nationality_name_en' => 'Vietnamese',
            ],
            [
                'nationality_name_lao' => 'ຈີນ',
                'nationality_name_en' => 'Chinese',
            ],
            [
                'nationality_name_lao' => 'ອາເມລິກາ',
                'nationality_name_en' => 'American',
            ],
            [
                'nationality_name_lao' => 'ຍີ່ປຸ່ນ',
                'nationality_name_en' => 'Japanese',
            ],
            [
                'nationality_name_lao' => 'ເກົາຫຼີ',
                'nationality_name_en' => 'Korean',
            ],
            [
                'nationality_name_lao' => 'ຝຣັ່ງ',
                'nationality_name_en' => 'French',
            ],
            [
                'nationality_name_lao' => 'ອັງກິດ',
                'nationality_name_en' => 'British',
            ],
            [
                'nationality_name_lao' => 'ອົດສະຕຣາລີ',
                'nationality_name_en' => 'Australian',
            ],
        ];
        
        foreach ($nationalities as $nationality) {
            Nationality::create($nationality);
        }
    }
}
