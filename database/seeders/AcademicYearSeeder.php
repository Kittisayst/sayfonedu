<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = Carbon::now()->year;
        
        // ສ້າງສົກຮຽນ 3 ປີຍ້ອນຫຼັງ, ປີປັດຈຸບັນ, ແລະ ປີຕໍ່ໄປ
        for ($i = -3; $i <= 1; $i++) {
            $year = $currentYear + $i;
            $nextYear = $year + 1;
            
            $yearName = "{$year}-{$nextYear}";
            $startDate = Carbon::createFromDate($year, 9, 1); // 1 ກັນຍາ
            $endDate = Carbon::createFromDate($nextYear, 7, 31); // 31 ກໍລະກົດ
            
            $status = 'completed';
            $isCurrent = false;
            
            if ($i == 0) { // ປີປັດຈຸບັນ
                $status = 'active';
                $isCurrent = true;
            } elseif ($i == 1) { // ປີໜ້າ
                $status = 'upcoming';
            }
            
            AcademicYear::create([
                'year_name' => $yearName,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_current' => $isCurrent,
                'status' => $status,
            ]);
        }
    }
}
