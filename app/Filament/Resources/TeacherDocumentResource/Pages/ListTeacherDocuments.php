<?php

namespace App\Filament\Resources\TeacherDocumentResource\Pages;

use App\Filament\Resources\TeacherDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherDocuments extends ListRecords
{
    protected static string $resource = TeacherDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
