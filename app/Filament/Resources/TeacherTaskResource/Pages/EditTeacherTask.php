<?php

namespace App\Filament\Resources\TeacherTaskResource\Pages;

use App\Filament\Resources\TeacherTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditTeacherTask extends EditRecord
{
    protected static string $resource = TeacherTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('ລຶບໜ້າວຽກ')->icon('heroicon-o-trash'),
        ];
    }

    public function getTitle(): string | Htmlable
    {
        return 'ແກ້ໄຂໜ້າວຽກ: ' . $this->record->title;
    }


}
