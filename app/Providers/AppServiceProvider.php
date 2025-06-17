<?php

namespace App\Providers;

use App\Filament\Auth\CustomLogin;
use App\Models\StudentSibling;
use App\Observers\StudentSiblingObserver;
use App\Services\SystemLogService;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LoginResponse;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('system-log', function ($app) {
            return new SystemLogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); // ເພີ່ມອັນນີ້
        App::setLocale('lo');
        Filament::serving(function () {
            app()->singleton(LoginResponse::class, CustomLogin::class);
        });
        StudentSibling::observe(StudentSiblingObserver::class);
        FilamentColor::register([
            'danger' => [
                50 => '254, 242, 242',
                100 => '254, 226, 226',
                200 => '254, 202, 202',
                300 => '252, 165, 165',
                400 => '248, 113, 113',
                500 => '239, 68, 68',
                600 => '220, 38, 38',
                700 => '185, 28, 28',
                800 => '153, 27, 27',
                900 => '127, 29, 29',
                950 => '69, 10, 10',
            ],
            'blue' => [
                50 => '239, 246, 255',
                100 => '219, 234, 254',
                200 => '191, 219, 254',
                300 => '147, 197, 253',
                400 => '96, 165, 250',
                500 => '59, 130, 246',
                600 => '37, 99, 235',
                700 => '29, 78, 216',
                800 => '30, 64, 175',
                900 => '30, 58, 138',
                950 => '23, 37, 84',
            ]
        ]);
    }
}
