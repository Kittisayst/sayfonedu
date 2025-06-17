<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('ແກ້ໄຂ')->icon('heroicon-o-pencil'),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return 'ຂໍ້ມູນນັກຮຽນ: ' . $this->record->first_name_lao . ' ' . $this->record->last_name_lao;
    }



}
