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
