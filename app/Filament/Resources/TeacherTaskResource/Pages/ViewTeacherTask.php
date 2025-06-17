<?php

namespace App\Filament\Resources\TeacherTaskResource\Pages;

use App\Filament\Resources\TeacherTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacherTask extends ViewRecord
{
    protected static string $resource = TeacherTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('mark_complete')
                ->label('ສຳເລັດວຽກ')
                ->icon('heroicon-o-check')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Textarea::make('completion_note')
                        ->label('ບັນທຶກການສຳເລັດວຽກ')
                        ->required(),
                    \Filament\Forms\Components\Radio::make('rating')
                        ->label('ຄະແນນປະເມີນຜົນ')
                        ->options([
                            1 => '1 - ຕ່ຳຫຼາຍ',
                            2 => '2 - ຕ່ຳ',
                            3 => '3 - ປານກາງ',
                            4 => '4 - ດີ',
                            5 => '5 - ດີຫຼາຍ',
                        ])
                        ->required(),
                ])
                ->action(function ($record, array $data): void {
                    $record->markAsCompleted($data['completion_note']);
                    $record->setRating($data['rating']);
                    $record->save();
                })
                ->visible(
                    fn($record): bool =>
                    $record->status !== 'completed'
                )
        ];
    }
}