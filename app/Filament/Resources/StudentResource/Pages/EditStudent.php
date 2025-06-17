<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('ເບີ່ງລາຍລະອຽດ')->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()->label('ລຶບຂໍ້ມູນນັກຮຽນ')->icon('heroicon-o-trash'),
            Actions\ForceDeleteAction::make()->label('ລຶບອອກສິດທິນີ້')->icon('heroicon-o-trash'),
            Actions\RestoreAction::make()->label('ກູ້ຄືນຂໍ້ມູນ')->icon('heroicon-o-arrow-path'),
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
        return 'ແກ້ໄຂຂໍ້ມູນນັກຮຽນ: ' . $this->record->first_name_lao . ' ' . $this->record->last_name_lao;
    }


}
