<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    /**
     * ✅ ກວດສອບສິດທິກ່ອນເຂົ້າໜ້າ
     */
    public function mount(int | string $record): void
    {
        parent::mount($record);

        // ກວດສອບສິດທິການແກ້ໄຂ
        if (!$this->record->canBeEdited()) {
            $this->redirectRoute('filament.admin.resources.payments.view', $this->record);
            
            Notification::make()
                ->title('ບໍ່ສາມາດແກ້ໄຂໄດ້')
                ->body('ການຊຳລະເງິນນີ້ບໍ່ສາມາດແກ້ໄຂໄດ້ ເນື່ອງຈາກສະຖານະຫຼືສິດທິຂອງທ່ານ')
                ->warning()
                ->send();
        }
    }

    /**
     * ✅ ຟອມສຳລັບແກ້ໄຂທີ່ມີ validation
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ຂໍ້ມູນພື້ນຖານ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('student_id')
                                    ->label('ນັກຮຽນ')
                                    ->relationship('student', 'first_name_lao')
                                    ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->student_code} - {$record->getFullName()}")
                                    ->searchable(['student_code', 'first_name_lao', 'last_name_lao'])
                                    ->preload()
                                    ->required()
                                    ->disabled(fn(): bool => !auth()->user()->hasRole('admin')) // ແອດມິນເທົ່ານັ້ນຖຶງແກ້ໄຂນັກຮຽນໄດ້
                                    ->columnSpan(1),

                                Select::make('academic_year_id')
                                    ->label('ສົກຮຽນ')
                                    ->relationship('academicYear', 'year_name')
                                    ->required()
                                    ->disabled(fn(): bool => !auth()->user()->hasRole('admin'))
                                    ->columnSpan(1),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('receipt_number')
                                    ->label('ເລກໃບບິນ')
                                    ->required()
                                    ->unique(Payment::class, 'receipt_number', ignoreRecord: true)
                                    ->disabled(fn(): bool => $this->record->isConfirmed()), // ບໍ່ໃຫ້ແກ້ຖ້າຢືນຢັນແລ້ວ

                                DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->required()
                                    ->maxDate(now())
                                    ->disabled(fn(): bool => $this->record->isConfirmed()),

                                Select::make('payment_status')
                                    ->label('ສະຖານະ')
                                    ->options(Payment::getStatusOptions())
                                    ->required()
                                    ->disabled(fn(): bool => !auth()->user()->hasRole('admin')),
                            ]),
                    ]),

                Section::make('ຈຳນວນເງິນ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('cash')
                                    ->label('ເງິນສົດ (ກີບ)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make('transfer')
                                    ->label('ເງິນໂອນ (ກີບ)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('food_money')
                                    ->label('ຄ່າອາຫານ (ກີບ)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make('late_fee')
                                    ->label('ຄ່າປັບຊ້າ (ກີບ)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1000)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                Select::make('discount_id')
                                    ->label('ສ່ວນຫຼຸດ')
                                    ->relationship('discount', 'discount_name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('discount_amount')
                                    ->label('ຈຳນວນສ່ວນຫຼຸດ (ກີບ)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(1000)
                                    ->readonly(),

                                TextInput::make('total_amount')
                                    ->label('ລວມທັງໝົດ (ກີບ)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->step(1000),
                            ]),
                    ]),

                Section::make('ເດືອນທີ່ຈ່າຍ')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                CheckboxList::make('tuition_months')
                                    ->label('ເດືອນຄ່າຮຽນ')
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get))
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if (empty($value)) {
                                                    $fail('ກະລຸນາເລືອກຢ່າງໜ້ອຍ 1 ເດືອນສຳລັບຄ່າຮຽນ');
                                                    return;
                                                }

                                                // ✅ ກວດສອບເດືອນຊ້ຳ (ຍົກເວັ້ນ record ປັດຈຸບັນ)
                                                if ($this->record->student_id && !empty($value)) {
                                                    $paidMonths = Payment::getPaidTuitionMonths(
                                                        $this->record->student_id,
                                                        $this->record->academic_year_id,
                                                        $this->record->payment_id // ✅ ຍົກເວັ້ນ record ປັດຈຸບັນ
                                                    );
                                                    $duplicates = array_intersect($value, $paidMonths);
                                                    if (!empty($duplicates)) {
                                                        $fail('ເດືອນ ' . implode(', ', $duplicates) . ' ໄດ້ຈ່າຍຄ່າຮຽນແລ້ວ');
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->columnSpan(1),

                                CheckboxList::make('food_months')
                                    ->label('ເດືອນຄ່າອາຫານ')
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get))
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                // ✅ ກວດສອບເດືອນຊ້ຳສຳລັບຄ່າອາຫານ
                                                if ($this->record->student_id && !empty($value)) {
                                                    $paidMonths = Payment::getPaidFoodMonths(
                                                        $this->record->student_id,
                                                        $this->record->academic_year_id,
                                                        $this->record->payment_id // ✅ ຍົກເວັ້ນ record ປັດຈຸບັນ
                                                    );
                                                    $duplicates = array_intersect($value, $paidMonths);
                                                    if (!empty($duplicates)) {
                                                        $fail('ເດືອນ ' . implode(', ', $duplicates) . ' ໄດ້ຈ່າຍຄ່າອາຫານແລ້ວ');
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->disabled(fn(): bool => $this->record->isConfirmed() && !auth()->user()->hasRole('admin')),

                Section::make('ຂໍ້ມູນເພີ່ມເຕີມ')
                    ->schema([
                        Select::make('received_by')
                            ->label('ຜູ້ຮັບເງິນ')
                            ->relationship('receiver', 'username')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->disabled(fn(): bool => !auth()->user()->hasRole('admin')),

                        Textarea::make('note')
                            ->label('ໝາຍເຫດ')
                            ->rows(3)
                            ->maxLength(500),

                        FileUpload::make('payment_images')
                            ->label('ຮູບໃບບິນ/ໃບໂອນ')
                            ->disk('public')
                            ->directory('payment_receipts')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120)
                            ->imagePreviewHeight('150')
                            ->multiple()
                            ->maxFiles(3)
                            ->columnSpanFull()
                            ->deleteUploadedFileUsing(function ($file) {
                                if (Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                            }),
                    ])
                    ->collapsible(),
            ]);
    }

    /**
     * ✅ ຄິດໄລ່ລວມເງິນແບບ real-time
     */
    private function calculateTotal(Set $set, Get $get): void
    {
        try {
            $cash = $this->parseAmount($get('cash') ?? 0);
            $transfer = $this->parseAmount($get('transfer') ?? 0);
            $foodMoney = $this->parseAmount($get('food_money') ?? 0);
            $lateFee = $this->parseAmount($get('late_fee') ?? 0);
            $discountId = $get('discount_id');

            $subtotal = $cash + $transfer + $foodMoney + $lateFee;
            $discountAmount = $this->calculateDiscountAmount($discountId, $subtotal);
            $total = max(0, $subtotal - $discountAmount);

            $set('discount_amount', $discountAmount);
            $set('total_amount', $total);

        } catch (\Exception $e) {
            Log::error('Error in calculateTotal during edit: ' . $e->getMessage());
        }
    }

    /**
     * ✅ ຄິດໄລ່ສ່ວນຫຼຸດ
     */
    private function calculateDiscountAmount($discountId, float $amount): float
    {
        if (!$discountId || $amount <= 0) {
            return 0;
        }

        try {
            $discount = \App\Models\Discount::find($discountId);
            if (!$discount || !$discount->is_active) {
                return 0;
            }

            if ($amount < ($discount->min_amount ?? 0)) {
                return 0;
            }

            if ($discount->discount_type === 'percentage') {
                $discountAmount = ($amount * $discount->discount_value) / 100;
                
                if ($discount->max_amount && $discountAmount > $discount->max_amount) {
                    $discountAmount = $discount->max_amount;
                }
                
                return $discountAmount;
            } elseif ($discount->discount_type === 'fixed') {
                return min($discount->discount_value, $amount);
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Error calculating discount during edit: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * ✅ Helper method ສຳລັບການແປງຄ່າເງິນ
     */
    private function parseAmount($value): float
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        $cleaned = str_replace([',', ' '], '', (string) $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * ✅ Actions ໃນໜ້າແກ້ໄຂ
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('ເບິ່ງ'),

            Actions\Action::make('confirm')
                ->label('ຢືນຢັນການຊຳລະ')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('ຢືນຢັນການຊຳລະເງິນ')
                ->modalDescription('ທ່ານຕ້ອງການຢືນຢັນການຊຳລະເງິນນີ້ແລ້ວຫຼືບໍ່?')
                ->modalSubmitActionLabel('ຢືນຢັນ')
                ->visible(fn(): bool => $this->record->isPending())
                ->action(function (): void {
                    $this->record->update(['payment_status' => 'confirmed']);
                    
                    Notification::make()
                        ->title('ຢືນຢັນສຳເລັດ')
                        ->body('ການຊຳລະເງິນໄດ້ຮັບການຢືນຢັນແລ້ວ')
                        ->success()
                        ->send();

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
                ->visible(fn(): bool => $this->record->isPending())
                ->action(function (): void {
                    $this->record->update(['payment_status' => 'cancelled']);
                    
                    Notification::make()
                        ->title('ຍົກເລີກສຳເລັດ')
                        ->body('ການຊຳລະເງິນໄດ້ຖືກຍົກເລີກແລ້ວ')
                        ->warning()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\DeleteAction::make()
                ->label('ລຶບ')
                ->visible(fn(): bool => $this->record->canBeDeleted()),
        ];
    }

    /**
     * ✅ ກ່ອນບັນທຶກ - ກວດສອບຂໍ້ມູນ
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ✅ ກວດສອບສິດທິອີກຄັ້ງ
        if (!$this->record->canBeEdited()) {
            throw new \Exception('ບໍ່ສາມາດແກ້ໄຂການຊຳລະເງິນນີ້ໄດ້');
        }

        // ✅ ກວດສອບຈຳນວນເງິນ
        $cash = $this->parseAmount($data['cash'] ?? 0);
        $transfer = $this->parseAmount($data['transfer'] ?? 0);
        $foodMoney = $this->parseAmount($data['food_money'] ?? 0);

        if ($cash <= 0 && $transfer <= 0 && $foodMoney <= 0) {
            throw new \Exception('ກະລຸນາປ້ອນຈຳນວນເງິນທີ່ຈະຊຳລະ');
        }

        // ✅ ກວດສອບເດືອນ
        $tuitionMonths = $data['tuition_months'] ?? [];
        $foodMonths = $data['food_months'] ?? [];

        if (empty($tuitionMonths) && empty($foodMonths)) {
            throw new \Exception('ກະລຸນາເລືອກເດືອນທີ່ຈະຊຳລະ');
        }

        return $data;
    }

    /**
     * ✅ ຫຼັງບັນທຶກສຳເລັດ
     */
    protected function afterSave(): void
    {
        Log::info('Payment updated', [
            'payment_id' => $this->record->payment_id,
            'receipt_number' => $this->record->receipt_number,
            'updated_by' => auth()->id(),
            'old_status' => $this->record->getOriginal('payment_status'),
            'new_status' => $this->record->payment_status,
        ]);

        Notification::make()
            ->title('ອັບເດດສຳເລັດ')
            ->body("ອັບເດດການຊຳລະເງິນເລກທີ {$this->record->receipt_number} ສຳເລັດແລ້ວ")
            ->success()
            ->send();
    }

    /**
     * ✅ ຫາມີ error ໃນການບັນທຶກ
     */
    protected function onValidationError(\Illuminate\Validation\ValidationException $exception): void
    {
        Notification::make()
            ->title('ຂໍ້ມູນບໍ່ຖືກຕ້ອງ')
            ->body('ກະລຸນາກວດສອບຂໍ້ມູນທີ່ປ້ອນ')
            ->danger()
            ->send();
    }

    /**
     * ✅ ກຳນົດ Title ຂອງໜ້າ
     */
    public function getTitle(): string
    {
        return "ແກ້ໄຂການຊຳລະ #{$this->record->receipt_number}";
    }

    /**
     * ✅ ກຳນົດ Breadcrumbs
     */
    public function getBreadcrumbs(): array
    {
        return [
            $this->getResource()::getUrl() => $this->getResource()::getNavigationLabel(),
            $this->getResource()::getUrl('view', ['record' => $this->record]) => "#{$this->record->receipt_number}",
            '#' => 'ແກ້ໄຂ',
        ];
    }
}