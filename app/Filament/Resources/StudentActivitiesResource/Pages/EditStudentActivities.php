<?php

namespace App\Filament\Resources\StudentActivitiesResource\Pages;

use App\Filament\Resources\StudentActivitiesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentActivities extends EditRecord
{
    protected static string $resource = StudentActivitiesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
