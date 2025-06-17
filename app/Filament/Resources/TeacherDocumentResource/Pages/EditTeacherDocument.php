<?php

namespace App\Filament\Resources\TeacherDocumentResource\Pages;

use App\Filament\Resources\TeacherDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherDocument extends EditRecord
{
    protected static string $resource = TeacherDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
