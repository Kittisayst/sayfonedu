<?php

namespace App\Filament\Resources\StudentBehaviorResource\Pages;

use App\Filament\Resources\StudentBehaviorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentBehavior extends EditRecord
{
    protected static string $resource = StudentBehaviorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
