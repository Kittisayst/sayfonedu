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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Storage;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    public array $paidTuitionMonths = [];
    public array $paidFoodMonths = [];
    public ?Student $selectedStudent = null;
    public $currentAcademicYear = null;

    /**
     * ✅ ກວດສອບສິດທິກ່ອນເຂົ້າໜ້າ
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
        }

        // ຕັ້ງຄ່າຂໍ້ມູນເບື້ອງຕົ້ນ
        $this->selectedStudent = $this->record->student;
        $this->currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // ໂຫຼດຂໍ້ມູນເດືອນທີ່ຈ່າຍແລ້ວ
        $this->loadPaidMonths();

        // ຕັ້ງຄ່າ receipt_number_view
        $this->form->fill([
            'receipt_number_view' => $this->record->receipt_number,
            'discount_amount_view' => number_format($this->record->discount_amount ?? 0),
            'total_amount_view' => number_format($this->record->total_amount ?? 0),
        ]);
    }

    /**
     * ✅ ໂຫຼດຂໍ້ມູນເດືອນທີ່ຈ່າຍແລ້ວ
     */
    private function loadPaidMonths(): void
    {
        if (!$this->selectedStudent || !$this->currentAcademicYear) {
            return;
        }

        try {
            // ຄ່າຮຽນທີ່ຈ່າຍແລ້ວ (ຍົກເວັ້ນການຊຳລະປັດຈຸບັນ)
            $this->paidTuitionMonths = Payment::where('student_id', $this->selectedStudent->id)
                ->where('academic_year_id', $this->currentAcademicYear->id)
                ->where('payment_status', 'confirmed')
                ->where('id', '!=', $this->record->id) // ຍົກເວັ້ນການຊຳລະປັດຈຸບັນ
                ->whereNotNull('tuition_months')
                ->pluck('tuition_months')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();

            // ຄ່າອາຫານທີ່ຈ່າຍແລ້ວ (ຍົກເວັ້ນການຊຳລະປັດຈຸບັນ)
            $this->paidFoodMonths = Payment::where('student_id', $this->selectedStudent->id)
                ->where('academic_year_id', $this->currentAcademicYear->id)
                ->where('payment_status', 'confirmed')
                ->where('id', '!=', $this->record->id) // ຍົກເວັ້ນການຊຳລະປັດຈຸບັນ
                ->whereNotNull('food_months')
                ->pluck('food_months')
                ->flatten()
                ->unique()
                ->values()
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Error loading paid months: ' . $e->getMessage());
            $this->paidTuitionMonths = [];
            $this->paidFoodMonths = [];
        }
    }

    /**
     * ✅ ສ້າງລາຍການເດືອນທີ່ມີສັນຍາລັກຈ່າຍແລ້ວ
     */
    private function getAvailableTuitionMonths(): array
    {
        $months = Payment::getMonthOptions();

        // ເພີ່ມສັນຍາລັກໃຫ້ເດືອນທີ່ຈ່າຍແລ້ວ
        foreach ($this->paidTuitionMonths as $paidMonth) {
            if (isset($months[$paidMonth])) {
                $months[$paidMonth] .= ' ✅ (ຈ່າຍແລ້ວ)';
            }
        }

        return $months;
    }

    private function getAvailableFoodMonths(): array
    {
        $months = Payment::getMonthOptions();

        // ເພີ່ມສັນຍາລັກໃຫ້ເດືອນທີ່ຈ່າຍແລ້ວ
        foreach ($this->paidFoodMonths as $paidMonth) {
            if (isset($months[$paidMonth])) {
                $months[$paidMonth] .= ' ✅ (ຈ່າຍແລ້ວ)';
            }
        }

        return $months;
    }

    /**
     * ✅ ສ້າງລາຍການທາງເລືອກສ່ວນຫຼຸດ
     */
    private function getDiscountOptions(): array
    {
        return \App\Models\Discount::where('is_active', true)
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
                                    ->options(fn() => $this->getAvailableTuitionMonths())
                                    ->disableOptionWhen(fn(string $value): bool => in_array($value, $this->paidTuitionMonths))
                                    ->columns(3)
                                    ->columnSpanFull()
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

                                                // ກວດສອບເດືອນຊ້ຳ
                                                if ($this->selectedStudent && !empty($value)) {
                                                    $paidMonths = Payment::getPaidTuitionMonths(
                                                        $this->selectedStudent->student_id,
                                                        $this->currentAcademicYear?->academic_year_id
                                                    );
                                                    $duplicates = array_intersect($value, $paidMonths);
                                                    if (!empty($duplicates)) {
                                                        $fail('ເດືອນ ' . implode(', ', $duplicates) . ' ໄດ້ຈ່າຍຄ່າຮຽນແລ້ວ');
                                                    }
                                                }
                                            };
                                        },
                                    ])
                            ])->columnSpan(1),

                        Fieldset::make("food_months_section")
                            ->label('ເດືອນຄ່າອາຫານ (ເປັນທາງເລືອກ)')
                            ->schema([
                                CheckboxList::make('food_months')
                                    ->hiddenLabel()
                                    ->options(fn() => $this->getAvailableFoodMonths())
                                    ->disableOptionWhen(fn(string $value): bool => in_array($value, $this->paidFoodMonths))
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get))
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                // ກວດສອບເດືອນຊ້ຳສຳລັບຄ່າອາຫານ
                                                if ($this->selectedStudent && !empty($value)) {
                                                    $paidMonths = Payment::getPaidFoodMonths(
                                                        $this->selectedStudent->student_id,
                                                        $this->currentAcademicYear?->academic_year_id
                                                    );
                                                    $duplicates = array_intersect($value, $paidMonths);
                                                    if (!empty($duplicates)) {
                                                        $fail('ເດືອນ ' . implode(', ', $duplicates) . ' ໄດ້ຈ່າຍຄ່າອາຫານແລ້ວ');
                                                    }
                                                }
                                            };
                                        },
                                    ])
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
                                    ->mask(\Filament\Support\RawJs::make('$money($input)'))
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
                                    ->mask(\Filament\Support\RawJs::make('$money($input)'))
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
                                    ->mask(\Filament\Support\RawJs::make('$money($input)'))
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
                                    ->mask(\Filament\Support\RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->required()
                                    ->default(now())
                                    ->maxDate(now()->addDay()),

                                FileUpload::make('image_path')
                                    ->label("ຮູບໃບໂອນ/ໃບບິນ")
                                    ->disk('public') // ໃຊ້ public disk
                                    ->directory('payment_receipts') // ໂຟນເດີໃສ່ໄຟລ์
                                    ->visibility('public') // ຕັ້ງໃຫ້ເປັນ public
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->imagePreviewHeight('150')
                                    ->multiple(true) // ອະນຸຍາດຫຼາຍຮູບ
                                    ->maxFiles(3)
                                    ->reorderable(true) // ສາມາດຈັດລຳດັບໄດ້
                                    ->previewable(true) // ສາມາດເບິ່ງຕົວຢ່າງໄດ້
                                    ->downloadable(true) // ສາມາດດາວໂຫລດໄດ້
                                    ->helperText('ອັບໂຫຼດໄດ້ສູງສຸດ 3 ຮູບ, ແຕ່ລະຮູບບໍ່ເກີນ 5MB (PNG, JPG, JPEG, WEBP)')
                                    ->columnSpanFull()
                                    ->deleteUploadedFileUsing(function ($file) {
                                        // ລົບໄຟລ์ອອກຈາກ storage
                                        if (Storage::disk('public')->exists($file)) {
                                            Storage::disk('public')->delete($file);
                                            return true;
                                        }
                                        return false;
                                    })
                                    ->getUploadedFileNameForStorageUsing(function ($file) {
                                        // ສ້າງຊື່ໄຟລ໌ໃໝ່ທີ່ບໍ່ຊ້ອນກັນ
                                        $extension = $file->getClientOriginalExtension();
                                        $fileName = time() . '_' . uniqid() . '.' . $extension;
                                        return $fileName;
                                    })
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
        $tuitionMonths = $get('tuition_months') ?? [];
        $foodMonths = $get('food_months') ?? [];
        $cash = (float) ($get('cash') ?? 0);
        $transfer = (float) ($get('transfer') ?? 0);
        $foodMoney = (float) ($get('food_money') ?? 0);
        $lateFee = (float) ($get('late_fee') ?? 0);
        $discountAmount = (float) ($get('discount_amount') ?? 0);

        // ຄິດໄລ່ຄ່າຮຽນ
        $tuitionAmount = 0;
        if ($this->selectedStudent && $this->currentAcademicYear) {
            $tuitionAmount = count($tuitionMonths) * $this->currentAcademicYear->tuition_fee;
        }

        // ຄິດໄລ່ຄ່າອາຫານ
        $foodAmount = 0;
        if ($this->selectedStudent && $this->currentAcademicYear && count($foodMonths) > 0) {
            $foodAmount = $foodMoney; // ໃຊ້ຈຳນວນທີ່ປ້ອນເຂົ້າ
        }

        // ຈຳນວນລວມ
        $total = $tuitionAmount + $foodAmount + $lateFee - $discountAmount;

        $set('total_amount', $total);
        $set('total_amount_view', number_format($total));
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