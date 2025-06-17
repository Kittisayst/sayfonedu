<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('ລຶບຜູ້ໃຊ້')->icon('heroicon-o-trash'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('ບັນທຶກການປ່ຽນແປງ')->icon('heroicon-o-check-circle'),
            $this->getCancelFormAction()->label('ຍົກເລີກ')->icon('heroicon-o-x-circle'),
        ];
    }
}
