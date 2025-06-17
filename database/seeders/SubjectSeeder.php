<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'subject_code' => 'LAO101',
                'subject_name_lao' => 'ພາສາລາວ 1',
                'subject_name_en' => 'Lao Language 1',
                'credit_hours' => 5,
                'description' => 'ພາສາລາວພື້ນຖານສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ພາສາລາວ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'LAO102',
                'subject_name_lao' => 'ພາສາລາວ 2',
                'subject_name_en' => 'Lao Language 2',
                'credit_hours' => 5,
                'description' => 'ພາສາລາວລະດັບກາງສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ພາສາລາວ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'MAT101',
                'subject_name_lao' => 'ຄະນິດສາດ 1',
                'subject_name_en' => 'Mathematics 1',
                'credit_hours' => 5,
                'description' => 'ຄະນິດສາດພື້ນຖານສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ຄະນິດສາດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'MAT102',
                'subject_name_lao' => 'ຄະນິດສາດ 2',
                'subject_name_en' => 'Mathematics 2',
                'credit_hours' => 5,
                'description' => 'ຄະນິດສາດລະດັບກາງສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ຄະນິດສາດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'ENG101',
                'subject_name_lao' => 'ພາສາອັງກິດ 1',
                'subject_name_en' => 'English 1',
                'credit_hours' => 4,
                'description' => 'ພາສາອັງກິດພື້ນຖານສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ພາສາຕ່າງປະເທດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'ENG102',
                'subject_name_lao' => 'ພາສາອັງກິດ 2',
                'subject_name_en' => 'English 2',
                'credit_hours' => 4,
                'description' => 'ພາສາອັງກິດລະດັບກາງສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ພາສາຕ່າງປະເທດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'SCI101',
                'subject_name_lao' => 'ວິທະຍາສາດ 1',
                'subject_name_en' => 'Science 1',
                'credit_hours' => 3,
                'description' => 'ວິທະຍາສາດພື້ນຖານສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ວິທະຍາສາດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'SCI102',
                'subject_name_lao' => 'ວິທະຍາສາດ 2',
                'subject_name_en' => 'Science 2',
                'credit_hours' => 3,
                'description' => 'ວິທະຍາສາດລະດັບກາງສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ວິທະຍາສາດ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'SOC101',
                'subject_name_lao' => 'ສັງຄົມສຶກສາ 1',
                'subject_name_en' => 'Social Studies 1',
                'credit_hours' => 3,
                'description' => 'ສັງຄົມສຶກສາພື້ນຖານສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ສັງຄົມສຶກສາ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'SOC102',
                'subject_name_lao' => 'ສັງຄົມສຶກສາ 2',
                'subject_name_en' => 'Social Studies 2',
                'credit_hours' => 3,
                'description' => 'ສັງຄົມສຶກສາລະດັບກາງສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ສັງຄົມສຶກສາ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'PED101',
                'subject_name_lao' => 'ພະລະສຶກສາ',
                'subject_name_en' => 'Physical Education',
                'credit_hours' => 2,
                'description' => 'ພະລະສຶກສາສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ພະລະສຶກສາ',
                'is_active' => true,
            ],
            [
                'subject_code' => 'ART101',
                'subject_name_lao' => 'ສິລະປະສຶກສາ',
                'subject_name_en' => 'Art Education',
                'credit_hours' => 2,
                'description' => 'ສິລະປະສຶກສາສຳລັບນັກຮຽນຊັ້ນປະຖົມ',
                'category' => 'ສິລະປະ',
                'is_active' => true,
            ],
        ];
        
        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
