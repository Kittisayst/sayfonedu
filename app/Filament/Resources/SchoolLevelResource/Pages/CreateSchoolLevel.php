<?php

namespace App\Filament\Resources\SchoolLevelResource\Pages;

use App\Filament\Resources\SchoolLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolLevel extends CreateRecord
{
    protected static string $resource = SchoolLevelResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
