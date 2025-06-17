<?php

namespace App\Filament\Resources\StudentParentResource\Pages;

use App\Filament\Resources\StudentParentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateStudentParent extends CreateRecord
{
    protected static string $resource = StudentParentResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'ເພີ່ມຜູ້ປົກຄອງ';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


}
