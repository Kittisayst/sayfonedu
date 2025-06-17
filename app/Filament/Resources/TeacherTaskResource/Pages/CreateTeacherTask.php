<?php

namespace App\Filament\Resources\TeacherTaskResource\Pages;

use App\Filament\Resources\TeacherTaskResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateTeacherTask extends CreateRecord
{
    protected static string $resource = TeacherTaskResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'ມອບໝາຍວຽກ';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
