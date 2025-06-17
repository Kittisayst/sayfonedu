<?php

namespace App\Filament\Resources\EthnicityResource\Pages;

use App\Filament\Resources\EthnicityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEthnicities extends ListRecords
{
    protected static string $resource = EthnicityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
