<?php

namespace App\Filament\Resources\StudentActivitiesResource\Pages;

use App\Filament\Resources\StudentActivitiesResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateStudentActivities extends CreateRecord
{
    protected static string $resource = StudentActivitiesResource::class;

    protected static ?string $title = "ສ້າງກິດຈະກຳນອກຫຼັກສູດ";

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make("createAnother")->hidden();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



}
