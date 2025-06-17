<?php

namespace App\Filament\Resources\StudentParentResource\Pages;

use App\Filament\Resources\StudentParentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditStudentParent extends EditRecord
{
    protected static string $resource = StudentParentResource::class;
    public function getTitle(): string|Htmlable
    {
        return 'ແກ້ໄຂຜູ້ປົກຄອງ';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('ລຶບຜູ້ປົກຄອງ')->icon('heroicon-o-trash'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('ບັນທຶກການປ່ຽນແປງ')->icon('heroicon-o-check-circle'),
            $this->getCancelFormAction()->label('ຍົກເລີກ')->icon('heroicon-o-x-circle'),
        ];
    }
}
