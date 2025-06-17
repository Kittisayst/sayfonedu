<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerLanguages([
                'lo' => 'ລາວ',
            ]);

            // ປ່ຽນຂໍ້ຄວາມປຸ່ມ
            Filament::registerRenderHook(
                'panels::actions.before',
                fn () => view('filament.custom-buttons', [
                    'saveText' => 'ບັນທຶກການປ່ຽນແປງ',
                    'cancelText' => 'ຍົກເລີກ',
                ]),
            );
        });
    }
} 