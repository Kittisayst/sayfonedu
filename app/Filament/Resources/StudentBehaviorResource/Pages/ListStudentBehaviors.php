<?php

namespace App\Filament\Resources\StudentBehaviorResource\Pages;

use App\Filament\Resources\StudentBehaviorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentBehaviors extends ListRecords
{
    protected static string $resource = StudentBehaviorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
