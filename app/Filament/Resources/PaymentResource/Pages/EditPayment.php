<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Discount;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Utils\Money;
use Filament\Actions;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Support\RawJs;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    public array $paidTuitionMonths = [];
    public array $paidFoodMonths = [];
    public ?Student $selectedStudent = null;
    public $currentAcademicYear = null;

    public ?array $data = [];

    public bool $showSaveConfirmModal = false;
    public array $pendingSaveData = [];

    /**
     * ✅ ແກ້ໄຂ mount() method ໃຫ້ຄົບຖ້ວນ
     */
    public function mount(int|string $record): void
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
            return;
        }

        // ຕັ້ງຄ່າຂໍ້ມູນເບື້ອງຕົ້ນ
        $this->selectedStudent = $this->record->student;
        $this->currentAcademicYear = $this->record->academicYear ?? AcademicYear::where('is_current', true)->first();



        // ✅ ຕື່ມຂໍ້ມູນໃສ່ form ໃຫ້ຄົບຖ້ວນ
        try {
            $sid = $this->record->student_id;
            $yid = $this->record->academic_year_id;

            // ໂຫຼດຂໍ້ມູນເດືອນທີ່ຈ່າຍແລ້ວ
            $piadTuitionM = $this->record->getTuitionMonthsSafe();
            $piadFoodM = $this->record->getFoodMonthsSafe();
            $tuitionM = $this->record->getPaidTuitionMonths($sid, $yid);
            $foodM = $this->record->getPaidFoodMonths($sid, $yid);
            $this->paidTuitionMonths = array_diff($tuitionM, $piadTuitionM);
            $this->paidFoodMonths = array_diff($foodM, $piadFoodM);

            $this->form->fill([
                'student_id' => $this->record->student_id,
                'academic_year_id' => $this->record->academic_year_id,
                'receipt_number' => $this->record->receipt_number,
                'receipt_number_view' => $this->record->receipt_number,
                'payment_date' => $this->record->payment_date,
                'cash' => Money::toInt($this->record->cash),
                'transfer' => Money::toInt($this->record->transfer),
                'food_money' => Money::toInt($this->record->food_money),
                'tuition_months' => $tuitionM,
                'food_months' => $foodM,
                'image_path' => $this->record->image_path,
                'discount_id' => $this->record->discount_id,
                'discount_amount' => Money::toInt($this->record->discount_amount),
                'discount_amount_view' => Money::toInt($this->record->discount_amount ?? 0),
                'late_fee' => Money::toInt($this->record->late_fee),
                'total_amount' => Money::toInt($this->record->total_amount),
                'total_amount_view' => Money::toInt($this->record->total_amount ?? 0),
                'note' => $this->record->note,
                'payment_status' => $this->record->payment_status,
            ]);

            // dd();
        } catch (\Exception $e) {
            Log::error('Error filling form in EditPayment mount: ' . $e->getMessage());

            Notification::make()
                ->title('ເກີດຂໍ້ຜິດພາດ')
                ->body('ບໍ່ສາມາດໂຫຼດຂໍ້ມູນການຊຳລະເງິນໄດ້')
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ ແກ້ໄຂ method ສຳລັບດຶງເດືອນທີ່ມີຢູ່
     */
    private function getAvailableTuitionMonths(): array
    {
        return Payment::getMonthOptions();
    }

    public function isMonthPaid(string $month, string $type = 'tuition'): bool
    {
        $paidMonths = $type === 'tuition' ? $this->paidTuitionMonths : $this->paidFoodMonths;
        return in_array($month, $paidMonths);
    }

    /**
     * ✅ ສ້າງລາຍການທາງເລືອກສ່ວນຫຼຸດ
     */
    private function getDiscountOptions(): array
    {
        return Discount::where('is_active', true)
            ->pluck('discount_name', 'discount_id')
            ->toArray();
    }

    /**
     * ✅ ຟອມແກ້ໄຂທີ່ມີໂຄງສ້າງຄືກັບ PaymentPage
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ເດືອນຄ່າຮຽນ ແລະ ຄ່າອາຫານ
                Grid::make(2)
                    ->schema([
                        Fieldset::make("tuition_months_section")
                            ->label('ເດືອນຄ່າຮຽນ')
                            ->schema([
                                CheckboxList::make('tuition_months')
                                    ->hiddenLabel()
                                    ->options(fn() => Payment::getMonthOptions())
                                    ->disableOptionWhen(fn(string $value): bool => $this->isMonthPaid($value, 'tuition'))
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->required()
                                    ->live()
                            ])->columnSpan(1),

                        Fieldset::make("food_months_section")
                            ->label('ເດືອນຄ່າອາຫານ (ເປັນທາງເລືອກ)')
                            ->schema([
                                CheckboxList::make('food_months')
                                    ->hiddenLabel()
                                    ->options(fn() => Payment::getMonthOptions())
                                    ->disableOptionWhen(fn(string $value): bool => $this->isMonthPaid($value, 'food'))
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get))
                            ])->columnSpan(1),
                    ]),

                // ການຊຳລະເງິນ
                Grid::make(2)
                    ->schema([
                        Fieldset::make("cash_payment_section")
                            ->label('ການຊຳລະເງິນສົດ')
                            ->schema([
                                TextInput::make('cash')
                                    ->label('ຈຳນວນເງິນສົດ (ກີບ)')
                                    ->prefixIcon('heroicon-o-banknotes')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('ປ້ອນຈຳນວນເງິນສົດ')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make('late_fee')
                                    ->label('ຄ່າປັບຈ່າຍຊ້າ (ກີບ)')
                                    ->prefixIcon('heroicon-o-exclamation-triangle')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                Select::make('discount_id')
                                    ->label("ສ່ວນຫຼຸດ")
                                    ->prefixIcon('heroicon-o-gift')
                                    ->placeholder("ເລືອກສ່ວນຫຼຸດ")
                                    ->options(fn() => $this->getDiscountOptions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),
                            ])
                            ->columns(1)
                            ->columnSpan(1),

                        Fieldset::make("transfer_payment_section")
                            ->label('ການຊຳລະເງິນໂອນ')
                            ->schema([
                                TextInput::make('transfer')
                                    ->label('ຈຳນວນເງິນໂອນ (ກີບ)')
                                    ->prefixIcon('heroicon-o-credit-card')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('ປ້ອນຈຳນວນເງິນໂອນ')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make("food_money")
                                    ->label('ຄ່າອາຫານ (ກີບ)')
                                    ->prefixIcon('heroicon-o-cake')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now()->addDay()),

                                FileUpload::make('image_path') // ✅ ປ່ຽນຈາກ 'image_path' ເປັນ 'payment_images'
                                    ->label("ຮູບໃບໂອນ/ໃບບິນ")
                                    ->disk('public')
                                    ->directory('payment_receipts')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->imagePreviewHeight('150')
                                    ->reorderable(true)
                                    ->previewable(true)
                                    ->downloadable(true)
                                    ->columnSpanFull()
                            ])
                            ->columns(1)
                            ->columnSpan(1),
                    ]),

                // ສະຫຼຸບການຊຳລະ
                Fieldset::make('payment_summary')
                    ->label('ສະຫຼຸບການຊຳລະ')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('receipt_number_view')
                                    ->label('ເລກໃບບິນ')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('discount_amount_view')
                                    ->label('ຈຳນວນສ່ວນຫຼຸດ')
                                    ->prefixIcon('heroicon-o-tag')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('ກີບ'),

                                TextInput::make('total_amount_view')
                                    ->label("ລວມທັງໝົດ")
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->suffix('ກີບ')
                                    ->extraAttributes(['class' => 'font-bold text-lg']),
                            ]),

                        // Hidden fields
                        Hidden::make('receipt_number'),
                        Hidden::make('discount_amount'),
                        Hidden::make('total_amount'),
                    ]),

                // ໝາຍເຫດ
                Textarea::make('note')
                    ->label("ໝາຍເຫດ")
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('ໝາຍເຫດເພີ່ມເຕີມ (ຖ້າມີ)')
                    ->columnSpanFull(),

                // Hidden fields for data integrity
                Hidden::make('student_id'),
                Hidden::make('academic_year_id'),
            ])
            ->statePath('data');
    }

    /**
     * ✅ ຄິດໄລ່ຈຳນວນເງິນລວມ (ເໝືອນກັບ PaymentPage)
     */
    private function calculateTotal(Set $set, Get $get): void
    {
        try {
            // ດຶງຄ່າຈາກ form
            $cash = Money::toInt($get('cash'));
            $transfer = Money::toInt($get('transfer'));
            $foodMoney = Money::toInt($get('food_money'));
            $lateFee = Money::toInt($get('late_fee'));
            $discountId = $get('discount_id');

            // ຄິດໄລ່ຈຳນວນກ່ອນສ່ວນຫຼຸດ
            $subtotal = $cash + $transfer + $foodMoney + $lateFee;

            // ຄິດໄລ່ສ່ວນຫຼຸດ
            $discountAmount = $this->calculateDiscountAmount($discountId, $subtotal);

            // ຄິດໄລ່ລວມສຸດທ້າຍ
            $total = max(0, $subtotal - $discountAmount);

            // ອັບເດດຄ່າໃນ form
            $set('discount_amount', $discountAmount);
            $set('discount_amount_view', Money::toLAK($discountAmount));
            $set('total_amount', $total);
            $set('total_amount_view', Money::toLAK($total));

        } catch (\Exception $e) {
            Log::error('Error in calculateTotal: ' . $e->getMessage());

            // ຕັ້ງຄ່າເປັນ 0 ຖ້າມີ error
            $set('discount_amount', 0);
            $set('discount_amount_view', '0');
            $set('total_amount', 0);
            $set('total_amount_view', '0');
        }
    }

    private function calculateDiscountAmount($discountId, float $amount): float
    {
        if (!$discountId || $amount <= 0) {
            return 0;
        }

        try {
            $discount = Discount::find($discountId);
            if (!$discount || !$discount->is_active) {
                return 0;
            }

            // ກວດສອບເງື່ອນໄຂຂັ້ນຕ່ຳ
            if ($amount < ($discount->min_amount ?? 0)) {
                return 0;
            }

            if ($discount->discount_type === 'percentage') {
                $discountAmount = ($amount * $discount->discount_value) / 100;

                // ກວດສອບຈຳນວນສູງສຸດ
                if ($discount->max_amount && $discountAmount > $discount->max_amount) {
                    $discountAmount = $discount->max_amount;
                }

                return $discountAmount;
            } elseif ($discount->discount_type === 'fixed') {
                return min($discount->discount_value, $amount);
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Error calculating discount: ' . $e->getMessage());
            return 0;
        }
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

    private function preparePaymentData(array $data): void
    {
        $this->pendingSaveData = [
            "student_id" => $this->selectedStudent->student_id,
            "academic_year_id" => $this->currentAcademicYear?->academic_year_id ?? 1,
            "payment_date" => $data['payment_date'] ?? now(),
            "receipt_number" => $data["receipt_number"],
            "cash" => Money::toInt($data['cash'] ?? 0),
            "transfer" => Money::toInt($data['transfer'] ?? 0),
            "food_money" => Money::toInt($data['food_money'] ?? 0),
            "tuition_months" => $data['tuition_months'] ?? [],
            "food_months" => $data['food_months'] ?? [],
            "discount_id" => $data['discount_id'] ?? null,
            "discount_amount" => Money::toInt($data['discount_amount'] ?? 0),
            "total_amount" => Money::toInt($data['total_amount'] ?? 0),
            "late_fee" => Money::toInt($data['late_fee'] ?? 0),
            "note" => $data['note'] ?? null,
            'received_by' => auth()->id(),
            "payment_status" => "pending",
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('ຍົກເລີກ')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url($this->getResource()::getUrl('view', ['record' => $this->record])),
            Actions\Action::make('edit_payment')
                ->label('ແກ້ໄຂການຊຳລະ')
                ->icon('heroicon-o-pencil')
                ->modalWidth('lg')
                ->fillForm(fn(): array => [
                    'amount' => $this->record->amount,
                    'payment_date' => $this->record->payment_date,
                    'transfer' => $this->record->transfer,
                ])
                ->form([
                    ViewField::make('edit-payment-modal')
                        ->view('filament.resources.payment-resource.pages.edit-payment-modal')
                ])
                ->action(function (array $data): void {
                    // $this->record->update($data);
                    dd($this->data);
                    // Notification::make()
                    //     ->title('ອັບເດດສຳເລັດ')
                    //     ->success()
                    //     ->send();
                })
        ];
    }

    
    /*
     * ✅ ກ່ອນບັນທຶກ - ກວດສອບແລະປັບແຕ່ງຂໍ້ມູນ
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // ກວດສອບສິດທິອີກຄັ້ງ
        if (!$this->record->canBeEdited()) {
            throw new \Exception('ບໍ່ສາມາດແກ້ໄຂການຊຳລະເງິນນີ້ໄດ້');
        }

        // ປັບແຕ່ງຂໍ້ມູນ
        $data['student_id'] = $this->selectedStudent->id;
        $data['academic_year_id'] = $this->currentAcademicYear->id;

        return $data;
    }

    /**
     * ✅ ຫຼັງບັນທຶກສຳເລັດ
     */
    protected function afterSave(): void
    {
        Notification::make()
            ->title('ບັນທຶກສຳເລັດ')
            ->body('ການແກ້ໄຂການຊຳລະເງິນສຳເລັດແລ້ວ')
            ->success()
            ->send();
    }
}