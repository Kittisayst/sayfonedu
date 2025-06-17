<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submit')
                ->label('ບັນທຶກຂໍ້ມູນນັກຮຽນ')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Action::make('cancel')
                ->label('ຍົກເລີກ')
                ->icon('heroicon-o-x-circle')
                ->color('gray'),
        ];
    }
    

    

}
