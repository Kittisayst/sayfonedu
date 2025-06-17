<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('ລຶບ')->icon('heroicon-o-trash'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('ບັນທຶກການປ່ຽນແປງ')->icon('heroicon-o-check-circle'),
            $this->getCancelFormAction()->label('ຍົກເລີກ')->icon('heroicon-o-x-circle'),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return 'ແກ້ໄຂ: ' . $this->record->first_name_lao . ' ' . $this->record->last_name_lao;
    }
} 