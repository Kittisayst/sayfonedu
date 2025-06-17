<?php

namespace App\Filament\Widgets;

use App\Models\SystemLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Carbon;

class SystemLogsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        return [
            Card::make('ຂໍ້ຜິດພາດທັງໝົດມື້ນີ້', SystemLog::whereIn('log_level', ['error', 'critical'])
                ->whereDate('created_at', $today)
                ->count())
                ->description('ຢູ່ໃນລະບົບ')
                ->descriptionIcon('heroicon-s-exclamation-circle')
                ->color('danger'),
                
            Card::make('ຂໍ້ຄວາມ logs ມື້ນີ້', SystemLog::whereDate('created_at', $today)->count())
                ->description(SystemLog::whereDate('created_at', $yesterday)->count() . ' ຈາກມື້ວານນີ້')
                ->descriptionIcon('heroicon-s-arrow-right')
                ->color('primary'),
                
            Card::make('ແຫຼ່ງທີ່ມາຂອງ logs', SystemLog::select('log_source')
                ->distinct()
                ->whereNotNull('log_source')
                ->count())
                ->description('ທີ່ແຕກຕ່າງກັນ')
                ->descriptionIcon('heroicon-s-code-bracket-square')
                ->color('secondary'),
        ];
    }
}