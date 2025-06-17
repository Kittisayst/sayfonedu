<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('ລຶບສິດບັນຊີ')->icon('heroicon-o-trash'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('ບັນທຶກການປ່ຽນແປງ')->icon('heroicon-o-check-circle'),
            $this->getCancelFormAction()->label('ຍົກເລີກ')->icon('heroicon-o-x-circle'),
        ];
    }
}
