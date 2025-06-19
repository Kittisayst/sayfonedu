<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Resources\AcademicYearResource;
use App\Filament\Resources\AnnouncementResource;
use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\ClassRoomResource;
use App\Filament\Resources\ClassSubjectResource;
use App\Filament\Resources\DiscountResource;
use App\Filament\Resources\DistrictResource;
use App\Filament\Resources\EthnicityResource;
use App\Filament\Resources\ExtracurricularActivityResource;
use App\Filament\Resources\MessageResource;
use App\Filament\Resources\NationalityResource;
use App\Filament\Resources\NotificationResource;
use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\ProvinceResource;
use App\Filament\Resources\ReligionResource;
use App\Filament\Resources\RequestResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\ScheduleResource;
use App\Filament\Resources\SchoolLevelResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\StudentAchievementResource;
use App\Filament\Resources\StudentActivitiesResource;
use App\Filament\Resources\StudentBehaviorResource;
use App\Filament\Resources\StudentEnrollmentResource;
use App\Filament\Resources\StudentInterestResource;
use App\Filament\Resources\StudentParentResource;
use App\Filament\Resources\StudentResource;
use App\Filament\Resources\SubjectResource;
use App\Filament\Resources\SystemLogResource;
use App\Filament\Resources\TeacherDocumentResource;
use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherTaskResource;
use App\Filament\Resources\UserActivityResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\VillageResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckPanelAccess;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(asset('images/favicon.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->brandName('ໂຮງຮຽນສາຍຝົນ')
            ->login(Login::class)
            ->profile()
            ->colors([
                'primary' => Color::Blue,
                'info' => Color::hex('#53eafd'),
            ])
            ->font('Noto Sans Lao', provider: GoogleFontProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            \Filament\Navigation\NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))
                                ->url(fn() => Pages\Dashboard::getUrl()),
                        ]),

                    NavigationGroup::make('ຈັດການຂໍ້ມູນນັກຮຽນ')
                        ->items([
                            ...StudentResource::getNavigationItems(),
                            ...RequestResource::getNavigationItems(),
                            ...StudentAchievementResource::getNavigationItems(),
                            ...StudentActivitiesResource::getNavigationItems(),
                            ...StudentBehaviorResource::getNavigationItems(),
                            ...StudentInterestResource::getNavigationItems(),
                            ...StudentParentResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການຊັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ')
                        ->items([
                            ...AcademicYearResource::getNavigationItems(),
                            ...ClassRoomResource::getNavigationItems(),
                            ...StudentEnrollmentResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການການເງິນ')
                        ->items([
                            ...DiscountResource::getNavigationItems(),
                            ...PaymentResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການຂໍ້ມູນຄູສອນ')
                        ->items([
                            ...TeacherDocumentResource::getNavigationItems(),
                            ...TeacherResource::getNavigationItems(),
                            ...TeacherTaskResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການການຮຽນການສອນ')
                        ->items([
                            ...AttendanceResource::getNavigationItems(),
                            ...ClassSubjectResource::getNavigationItems(),
                            ...ExtracurricularActivityResource::getNavigationItems(),
                            ...ScheduleResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການການສື່ສານ')
                        ->items([
                            ...AnnouncementResource::getNavigationItems(),
                            ...MessageResource::getNavigationItems(),
                            ...NotificationResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການຜູ້ໃຊ້')
                        ->items([
                            ...PermissionResource::getNavigationItems(),
                            ...RoleResource::getNavigationItems(),
                            ...UserActivityResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຈັດການຂໍ້ມູນພື້ນຖານ')
                        ->items([
                            ...DistrictResource::getNavigationItems(),
                            ...EthnicityResource::getNavigationItems(),
                            ...NationalityResource::getNavigationItems(),
                            ...ProvinceResource::getNavigationItems(),
                            ...ReligionResource::getNavigationItems(),
                            ...SchoolLevelResource::getNavigationItems(),
                            ...VillageResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('ຕັ້ງຄ່າ ແລະ ບຳລຸງຮັກສາ')
                        ->items([
                            ...SettingResource::getNavigationItems(),
                            ...SystemLogResource::getNavigationItems(),
                        ]),
                ]);
            })
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckPanelAccess::class,
            ])
        ;
    }
}
