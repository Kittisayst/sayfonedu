<?php

namespace App\Filament\Resources\StudentInterestResource\Pages;

use App\Filament\Resources\StudentInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentInterests extends ListRecords
{
    protected static string $resource = StudentInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
