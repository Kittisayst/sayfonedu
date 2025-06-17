<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class SchoolOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null; // ຖ້າບໍ່ຕ້ອງການ refresh ອັດຕະໂນມັດ

    protected function getStats(): array
    {
        // Cache ຂໍ້ມູນເພື່ອປັບປຸງປະສິດທິພາບ - ເກັບຄ່າໄວ້ 1 ຊົ່ວໂມງ
        return Cache::remember('school_overview_stats', 3600, function () {
            // ຮັບຂໍ້ມູນສົກຮຽນປັດຈຸບັນ
            $currentAcademicYear = AcademicYear::where('is_current', true)->first();
            $academicYearStatus = $currentAcademicYear ? $currentAcademicYear->status : 'ບໍ່ມີສົກຮຽນປັດຈຸບັນ';
            $academicYearName = $currentAcademicYear ? $currentAcademicYear->year_name : 'ບໍ່ມີຂໍ້ມູນ';

            // ຄິດໄລ່ຈຳນວນນັກຮຽນຕາມສະຖານະ
            $activeStudents = Student::where('status', 'active')->count();
            $inactiveStudents = Student::where('status', 'inactive')->count();
            $transferredStudents = Student::where('status', 'transferred')->count();
            $graduatedStudents = Student::where('status', 'graduated')->count();
            $totalStudents = $activeStudents + $inactiveStudents + $transferredStudents + $graduatedStudents;

            // ຄິດໄລ່ຈຳນວນຄູຕາມປະເພດສັນຍາ
            $fullTimeTeachers = Teacher::where('contract_type', 'full_time')->where('status', 'active')->count();
            $partTimeTeachers = Teacher::where('contract_type', 'part_time')->where('status', 'active')->count();
            $contractTeachers = Teacher::where('contract_type', 'contract')->where('status', 'active')->count();
            $totalTeachers = $fullTimeTeachers + $partTimeTeachers + $contractTeachers;

            // ຄິດໄລ່ຈຳນວນຫ້ອງຮຽນຕາມລະດັບຊັ້ນ
            $totalClasses = SchoolClass::where('status', 'active');

            if ($currentAcademicYear) {
                $totalClasses = $totalClasses->where('academic_year_id', $currentAcademicYear->academic_year_id);
            }

            $totalClasses = $totalClasses->count();

            // ສະແດງອັດຕາສ່ວນນັກຮຽນ vs ຄູສອນ
            $studentTeacherRatio = $totalTeachers > 0 ? round($activeStudents / $totalTeachers, 1) : 0;

            return [
                // ນັກຮຽນທັງໝົດ
                Stat::make('ນັກຮຽນທັງໝົດ', $totalStudents)
                    ->description('ນັກຮຽນທີ່ກຳລັງຮຽນປັດຈຸບັນ: ' . $activeStudents)
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->chart([
                        $activeStudents,
                        $inactiveStudents,
                        $transferredStudents,
                        $graduatedStudents,
                    ])
                    ->color('success'),

                // ຄູສອນທັງໝົດ
                Stat::make('ຄູສອນທັງໝົດ', $totalTeachers)
                    ->description("ອັດຕາສ່ວນນັກຮຽນຕໍ່ຄູ: $studentTeacherRatio:1")
                    ->descriptionIcon('heroicon-m-user-group')
                    ->chart([
                        $fullTimeTeachers,
                        $partTimeTeachers,
                        $contractTeachers,
                    ])
                    ->color('primary'),

                // ຫ້ອງຮຽນທັງໝົດ
                Stat::make('ຫ້ອງຮຽນທັງໝົດ', $totalClasses)
                    ->description("ສົກຮຽນ: $academicYearName ($academicYearStatus)")
                    ->descriptionIcon('heroicon-m-building-office-2')
                    ->color('warning'),
            ];
        });
    }
}