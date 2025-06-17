<?php

namespace App\Filament\Resources\StudentInterestResource\Pages;

use App\Filament\Resources\StudentInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentInterest extends EditRecord
{
    protected static string $resource = StudentInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
