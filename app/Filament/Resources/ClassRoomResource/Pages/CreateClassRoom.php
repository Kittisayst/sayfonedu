<?php

namespace App\Filament\Resources\ClassRoomResource\Pages;

use App\Filament\Resources\ClassRoomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClassRoom extends CreateRecord
{
    protected static string $resource = ClassRoomResource::class;

    protected static ?string $title = 'ສ້າງຫ້ອງຮຽນ';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
   
}
