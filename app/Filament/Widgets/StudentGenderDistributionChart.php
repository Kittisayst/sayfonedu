<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StudentGenderDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'ນັກຮຽນແຍກຕາມເພດ';
    
    protected int | string | array $columnSpan = 'md:col-span-1';

    protected function getData(): array
    {
        $studentsByGender = Student::where('status', 'active')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        // ແປງຄ່າ keys ເປັນຊື່ທີ່ຈະສະແດງ
        $labels = [
            'male' => 'ຊາຍ',
            'female' => 'ຍິງ',
            'other' => 'ອື່ນໆ',
        ];

        $datasets = [];
        $formattedLabels = [];
        $colors = ['#1A56DB', '#E74694', '#9061F9'];
        $data = [];

        foreach ($labels as $key => $label) {
            $formattedLabels[] = $label;
            $data[] = $studentsByGender[$key] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'ນັກຮຽນແຍກຕາມເພດ',
                    'data' => $data,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $formattedLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // ຫຼື 'pie' ສຳລັບວົງກົມແບບເຕັມ
    }
}