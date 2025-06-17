<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'ເພີ່ມນັກຮຽນ';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
