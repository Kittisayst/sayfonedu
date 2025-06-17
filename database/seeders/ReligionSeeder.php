<?php

namespace Database\Seeders;

use App\Models\Religion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReligionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $religions = [
            [
                'religion_name_lao' => 'ພຸດທະສາສະໜາ',
                'religion_name_en' => 'Buddhism',
            ],
            [
                'religion_name_lao' => 'ຄຣິດສະຕຽນ',
                'religion_name_en' => 'Christianity',
            ],
            [
                'religion_name_lao' => 'ອິສລາມ',
                'religion_name_en' => 'Islam',
            ],
            [
                'religion_name_lao' => 'ຫີນດູ',
                'religion_name_en' => 'Hinduism',
            ],
            [
                'religion_name_lao' => 'ບໍ່ມີສາສະໜາ',
                'religion_name_en' => 'No Religion',
            ],
            [
                'religion_name_lao' => 'ອື່ນໆ',
                'religion_name_en' => 'Other',
            ],
        ];
        
        foreach ($religions as $religion) {
            Religion::create($religion);
        }
    }
}
