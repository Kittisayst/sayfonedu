<?php

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class Dashboard extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
}