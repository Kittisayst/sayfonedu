<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * ກຳນົດ Actions ໃນໜ້າເບິ່ງ
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_receipt')
                ->label('ພິມໃບບິນ')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn(): string => route('print.receipt', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('confirm')
                ->label('ຢືນຢັນການຊຳລະ')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('ຢືນຢັນການຊຳລະເງິນ')
                ->modalDescription('ທ່ານຕ້ອງການຢືນຢັນການຊຳລະເງິນນີ້ແລ້ວຫຼືບໍ່?')
                ->modalSubmitActionLabel('ຢືນຢັນ')
                ->visible(fn(): bool => $this->record->payment_status === 'pending')
                ->action(function (): void {
                    $this->record->update(['payment_status' => 'confirmed']);

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('cancel')
                ->label('ຍົກເລີກການຊຳລະ')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('ຍົກເລີກການຊຳລະເງິນ')
                ->modalDescription('ທ່ານຕ້ອງການຍົກເລີກການຊຳລະເງິນນີ້ແລ້ວຫຼືບໍ່?')
                ->modalSubmitActionLabel('ຍົກເລີກ')
                ->visible(fn(): bool => $this->record->payment_status === 'pending')
                ->action(function (): void {
                    $this->record->update(['payment_status' => 'cancelled']);

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\EditAction::make()
                ->label('ແກ້ໄຂ')
                ->visible(
                    fn(): bool =>
                    $this->record->payment_status === 'pending' ||
                    auth()->user()->hasRole('admin')
                ),

            Actions\DeleteAction::make()
                ->label('ລຶບ')
                ->visible(fn(): bool => auth()->user()->hasRole('admin')),
        ];
    }

    /**
     * ກຳນົດວ່າໃຊ້ Infolist ແທນ Form
     */
    public function infolist(Infolist $infolist): Infolist
    {
        return $this->getResource()::infolist($infolist);
    }

    /**
     * ກຳນົດ Title ຂອງໜ້າ
     */
    public function getTitle(): string
    {
        return "ລາຍລະອຽດການຊຳລະ #{$this->record->receipt_number}";
    }

    /**
     * ກຳນົດ Breadcrumbs
     */
    public function getBreadcrumbs(): array
    {
        return [
            $this->getResource()::getUrl() => $this->getResource()::getNavigationLabel(),
            '#' => 'ລາຍລະອຽດ',
        ];
    }
}