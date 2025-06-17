<?php

namespace App\Filament\Resources\TeacherTaskResource\Pages;

use App\Filament\Resources\TeacherTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherTasks extends ListRecords
{
    protected static string $resource = TeacherTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('ເພີ່ມໜ້າວຽກ')->icon('heroicon-o-plus-circle'),
        ];
    }

    
}
