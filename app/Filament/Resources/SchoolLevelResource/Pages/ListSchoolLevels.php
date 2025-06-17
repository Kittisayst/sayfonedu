<?php

namespace App\Filament\Resources\SchoolLevelResource\Pages;

use App\Filament\Resources\SchoolLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchoolLevels extends ListRecords
{
    protected static string $resource = SchoolLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
