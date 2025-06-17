<?php

namespace App\Filament\Resources\SchoolLevelResource\Pages;

use App\Filament\Resources\SchoolLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolLevel extends EditRecord
{
    protected static string $resource = SchoolLevelResource::class;

    protected static ?string $title = 'ແກ້ໄຂຂະແໜງ';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
