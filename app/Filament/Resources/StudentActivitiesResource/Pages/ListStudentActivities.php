<?php

namespace App\Filament\Resources\StudentActivitiesResource\Pages;

use App\Filament\Resources\StudentActivitiesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentActivities extends ListRecords
{
    protected static string $resource = StudentActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('ເພີ່ມກິດຈະກຳນອກຫຼັກສູດ')->icon('heroicon-o-plus'),
        ];
    }
}
