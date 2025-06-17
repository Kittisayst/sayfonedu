<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentImage;
use App\Models\Student;
use App\Models\Discount;
use App\Models\AcademicYear;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\RawJs;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PaymentPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static string $resource = PaymentResource::class;
    protected static string $view = 'filament.resources.payment-resource.pages.payment-page';

    /**
     * ຟອມ Data
     */
    public ?array $data = [];

    /**
     * ຕົວແປພື້ນຖານ
     */
    public string $search_val = '';
    public ?Student $selectedStudent = null;
    public ?Collection $foundStudents;
    public ?string $profile_image = null;
    public $currentAcademicYear = null;

    public $pendingPaymentData = [];
    public $showConfirmModal = false;

    public array $paidTuitionMonths = [];
    public array $paidFoodMonths = [];

    public ?Collection $paymentHistory;

    /**
     * ເລີ່ມຕົ້ນເມື່ອໂຫຼດໜ້າ
     */
    public function mount(): void
    {
        $this->foundStudents = collect();
        $this->currentAcademicYear = AcademicYear::where('is_current', true)->first();

        // ຕັ້ງຄ່າ default values
        $receiptNo = Payment::generateReceiptNumber();

        $this->form->fill([
            "receipt_number" => $receiptNo,
            "receipt_number_view" => $receiptNo,
            'cash' => 0,
            'transfer' => 0,
            'payment_date' => now(),
            'late_fee' => 0,
            'food_money' => 0,
            'tuition_months' => [],
            'food_months' => [],
            'discount_id' => null,
            'discount_amount' => 0,
            'discount_amount_view' => '0',
            'total_amount' => 0,
            'total_amount_view' => '0',
            'note' => null,
            'image_path' => []
        ]);
    }

    private function getAvailableTuitionMonths(): array
    {
        $months = Payment::getMonthOptions();

        // ✅ ເພີ່ມສັນຍາລັກໃຫ້ເດືອນທີ່ຈ່າຍແລ້ວ
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

        // ✅ ເພີ່ມສັນຍາລັກໃຫ້ເດືອນທີ່ຈ່າຍແລ້ວ
        foreach ($this->paidFoodMonths as $paidMonth) {
            if (isset($months[$paidMonth])) {
                $months[$paidMonth] .= ' ✅ (ຈ່າຍແລ້ວ)';
            }
        }

        return $months;
    }

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
                                    ->options($this->getAvailableTuitionMonths())
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
                                    ->options($this->getAvailableFoodMonths())
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
                                    ->options($this->getDiscountOptions())
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
                                        // ສ້າງຊື່ໄຟລ์ໃໝ່ທີ່ບໍ່ຊ້ອນກັນ
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
                                    ->disabled(),

                                TextInput::make('discount_amount_view')
                                    ->label('ຈຳນວນສ່ວນຫຼຸດ')
                                    ->prefixIcon('heroicon-o-tag')
                                    ->disabled()
                                    ->suffix('ກີບ'),

                                TextInput::make('total_amount_view')
                                    ->label("ລວມທັງໝົດ")
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->disabled()
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
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Payment::query()
                    ->when($this->selectedStudent, function ($query) {
                        $query->where('student_id', $this->selectedStudent->student_id)
                            ->where('academic_year_id', $this->currentAcademicYear?->academic_year_id);
                    })
                    ->whereIn('payment_status', ['confirmed', 'pending'])
                    ->with(['receiver', 'discount'])
                    ->latest('payment_date')
            )
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('ເລກໃບບິນ')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('payment_date')
                    ->label('ວັນທີຊຳລະ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('ຈຳນວນເງິນ')
                    ->money('LAK')
                    ->color('success')
                    ->weight('bold'),

                BadgeColumn::make('payment_status')
                    ->label('ສະຖານະ')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'ລໍຖ້າຢືນຢັນ',
                        'confirmed' => 'ຢືນຢັນແລ້ວ',
                        'cancelled' => 'ຍົກເລີກ',
                        'refunded' => 'ຄືນເງິນ',
                        default => $state,
                    }),

                TextColumn::make('tuition_months')
                    ->label('ເດືອນຄ່າຮຽນ')
                    ->getStateUsing(function (Payment $record): string {
                        $months = $record->getTuitionMonthsSafe();
                        if (empty($months))
                            return '-';

                        $monthNames = array_map(fn($month) => Payment::getMonthName($month), $months);
                        return implode(', ', $monthNames);
                    })
                    ->badge()
                    ->color('success')
                    ->separator(','),

                TextColumn::make('food_months')
                    ->label('ເດືອນຄ່າອາຫານ')
                    ->getStateUsing(function (Payment $record): string {
                        $months = $record->getFoodMonthsSafe();
                        if (empty($months))
                            return '-';

                        $monthNames = array_map(fn($month) => Payment::getMonthName($month), $months);
                        return implode(', ', $monthNames);
                    })
                    ->badge()
                    ->color('info')
                    ->separator(','),

                TextColumn::make('receiver.username')
                    ->label('ຜູ້ຮັບເງິນ')
                    ->default('ບໍ່ມີຂໍ້ມູນ'),

                TextColumn::make('note')
                    ->label('ໝາຍເຫດ')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('ເບິ່ງ')
                    ->url(fn(Payment $record): string => route('filament.admin.resources.payments.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('ບໍ່ມີປະຫວັດການຊຳລະ')
            ->emptyStateDescription('ນັກຮຽນຄົນນີ້ຍັງບໍ່ໄດ້ຊຳລະຄ່າທຳນຽມໃນສົກຮຽນນີ້')
            ->emptyStateIcon('heroicon-o-clock')
            ->striped()
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }

    /**
     * ✅ ປັບປຸງການຄິດໄລ່ລວມເງິນ
     */
    private function calculateTotal(Set $set, Get $get): void
    {
        try {
            // ດຶງຄ່າຈາກ form
            $cash = $this->parseAmount($get('cash'));
            $transfer = $this->parseAmount($get('transfer'));
            $foodMoney = $this->parseAmount($get('food_money'));
            $lateFee = $this->parseAmount($get('late_fee'));
            $discountId = $get('discount_id');

            // ຄິດໄລ່ຈຳນວນກ່ອນສ່ວນຫຼຸດ
            $subtotal = $cash + $transfer + $foodMoney + $lateFee;

            // ຄິດໄລ່ສ່ວນຫຼຸດ
            $discountAmount = $this->calculateDiscountAmount($discountId, $subtotal);

            // ຄິດໄລ່ລວມສຸດທ້າຍ
            $total = max(0, $subtotal - $discountAmount);

            // ອັບເດດຄ່າໃນ form
            $set('discount_amount', $discountAmount);
            $set('discount_amount_view', $this->formatMoney($discountAmount));
            $set('total_amount', $total);
            $set('total_amount_view', $this->formatMoney($total));

            Log::info('Payment calculation', [
                'cash' => $cash,
                'transfer' => $transfer,
                'food_money' => $foodMoney,
                'late_fee' => $lateFee,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            Log::error('Error in calculateTotal: ' . $e->getMessage());

            // ຕັ້ງຄ່າເປັນ 0 ຖ້າມີ error
            $set('discount_amount', 0);
            $set('discount_amount_view', '0');
            $set('total_amount', 0);
            $set('total_amount_view', '0');
        }
    }

    /**
     * ✅ ປັບປຸງການຄິດໄລ່ສ່ວນຫຼຸດ
     */
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
     * ✅ Helper method ສຳລັບການແປງຄ່າເງິນ
     */
    private function parseAmount($value): float
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        // ລຶບ comma ແລະ spaces
        $cleaned = str_replace([',', ' '], '', (string) $value);

        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * ✅ Helper method ສຳລັບການຈັດຮູບແບບເງິນ
     */
    private function formatMoney(float $amount): string
    {
        return number_format($amount, 0, '.', ',');
    }

    /**
     * ✅ ດຶງລາຍການສ່ວນຫຼຸດທີ່ເປີດໃຊ້ງານ
     */
    private function getDiscountOptions(): array
    {
        try {
            return Discount::where('is_active', true)
                ->orderBy('discount_name')
                ->pluck('discount_name', 'discount_id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting discount options: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ຄົ້ນຫາແບບ real-time ເມື່ອມີການພິມ
     */
    public function updatedSearchVal(): void
    {
        if (empty($this->search_val) || strlen($this->search_val) < 2) {
            $this->resetSearchResults();
            return;
        }

        $this->foundStudents = $this->searchStudents($this->search_val);
        $this->resetSelectedStudent();
    }

    /**
     * ຄົ້ນຫານັກຮຽນຕາມຄຳຄົ້ນຫາ
     */
    protected function searchStudents(string $searchTerm): Collection
    {
        return Student::query()
            ->when(is_numeric($searchTerm), function (Builder $query) use ($searchTerm) {
                return $query->where('student_code', 'like', $searchTerm . '%');
            }, function (Builder $query) use ($searchTerm) {
                return $query->where(function (Builder $subQuery) use ($searchTerm) {
                    $subQuery->where('first_name_lao', 'like', '%' . $searchTerm . '%')
                        ->orWhere('last_name_lao', 'like', '%' . $searchTerm . '%')
                        ->orWhereRaw("CONCAT(first_name_lao, ' ', last_name_lao) LIKE ?", ['%' . $searchTerm . '%']);
                });
            })
            ->orderBy('student_code')
            ->limit(10)
            ->get();
    }

    /**
     * ເລືອກນັກຮຽນຈາກຜົນການຄົ້ນຫາ
     */
    public function selectStudent($studentId): void
    {
        if (!$studentId) {
            return;
        }

        $this->selectedStudent = Student::with([
            'parents',
            'enrollments.schoolClass'
        ])->find($studentId);

        if ($this->selectedStudent) {
            $this->profile_image = $this->selectedStudent->profile_image;
            $this->paidTuitionMonths = Payment::getPaidTuitionMonths(
                $this->selectedStudent->student_id,
                $this->currentAcademicYear?->academic_year_id
            );
            $this->paidFoodMonths = Payment::getPaidFoodMonths(
                $this->selectedStudent->student_id,
                $this->currentAcademicYear?->academic_year_id
            );
            $this->loadPaymentHistory();
            $this->resetSearchInterface();
            $this->notifyStudentSelected();

        }
    }

    /**
     * ຄົ້ນຫາເມື່ອກົດປຸ່ມ Enter
     */
    public function performSearch(): void
    {
        $this->updatedSearchVal();
    }

    /**
     * ລ້າງຜົນການຄົ້ນຫາ
     */
    protected function resetSearchResults(): void
    {
        $this->foundStudents = collect();
    }

    /**
     * ລ້າງຂໍ້ມູນນັກຮຽນທີ່ເລືອກ
     */
    protected function resetSelectedStudent(): void
    {
        $this->selectedStudent = null;
        $this->profile_image = null;
    }

    /**
     * ລ້າງໜ້າຈໍຄົ້ນຫາເມື່ອເລືອກນັກຮຽນແລ້ວ
     */
    protected function resetSearchInterface(): void
    {
        $this->foundStudents = collect();
        $this->search_val = '';
    }

    /**
     * ສະແດງການແຈ້ງເຕືອນເມື່ອເລືອກນັກຮຽນ
     */
    protected function notifyStudentSelected(): void
    {
        $studentName = $this->selectedStudent->getFullName();

        Notification::make()
            ->title('ເລືອກນັກຮຽນສຳເລັດ')
            ->body("ເລືອກນັກຮຽນ: {$studentName}")
            ->success()
            ->send();
    }

    /**
     * ✅ ປັບປຸງ Method ສຳລັບ Header Action
     */
    public function processPaymentAction(): void
    {
        try {
            // Validate form
            $data = $this->form->getState();

            if (!$this->selectedStudent) {
                throw new \Exception('ກະລຸນາເລືອກນັກຮຽນກ່ອນ');
            }

            // Additional validation
            $this->validatePaymentData($data);

            // Prepare payment data
            $this->preparePaymentData($data);

            // Show confirmation modal
            $this->showConfirmModal = true;

        } catch (\Exception $e) {
            Notification::make()
                ->title('ເກີດຂໍ້ຜິດພາດ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ ປັບປຸງ Validate payment data
     */
    private function validatePaymentData(array $data): void
    {
        // ກວດສອບວ່າມີການຊຳລະເງິນຢ່າງໜ້ອຍ 1 ປະເພດ
        $cash = $this->parseAmount($data['cash'] ?? 0);
        $transfer = $this->parseAmount($data['transfer'] ?? 0);
        $foodMoney = $this->parseAmount($data['food_money'] ?? 0);

        if ($cash <= 0 && $transfer <= 0 && $foodMoney <= 0) {
            throw new \Exception('ກະລຸນາປ້ອນຈຳນວນເງິນທີ່ຈະຊຳລະ (ເງິນສົດ, ເງິນໂອນ, ຫຼື ຄ່າອາຫານ)');
        }

        // ກວດສອບຈຳນວນເງິນລວມ
        $totalAmount = $this->parseAmount($data['total_amount'] ?? 0);
        if ($totalAmount <= 0) {
            throw new \Exception('ຈຳນວນເງິນລວມຕ້ອງມາກກວ່າ 0 ກີບ');
        }

        // ກວດສອບເດືອນ
        $tuitionMonths = $data['tuition_months'] ?? [];
        $foodMonths = $data['food_months'] ?? [];

        if (empty($tuitionMonths) && empty($foodMonths)) {
            throw new \Exception('ກະລຸນາເລືອກເດືອນທີ່ຈະຊຳລະ (ຄ່າຮຽນ ຫຼື ຄ່າອາຫານ)');
        }

        // ຖ້າມີຄ່າອາຫານ ແຕ່ບໍ່ມີເດືອນຄ່າອາຫານ
        if ($foodMoney > 0 && empty($foodMonths)) {
            throw new \Exception('ກະລຸນາເລືອກເດືອນຄ່າອາຫານທີ່ຈະຊຳລະ');
        }

        // ກວດສອບວັນທີ
        if (empty($data['payment_date'])) {
            throw new \Exception('ກະລຸນາເລືອກວັນທີຊຳລະ');
        }
    }

    /**
     * ✅ ປັບປຸງ Prepare payment data for confirmation
     */
    private function preparePaymentData(array $data): void
    {
        $this->pendingPaymentData = [
            "student_id" => $this->selectedStudent->student_id,
            "academic_year_id" => $this->currentAcademicYear?->academic_year_id ?? 1,
            "payment_date" => $data['payment_date'] ?? now(),
            "receipt_number" => $data["receipt_number"] ?? Payment::generateReceiptNumber(),
            "cash" => $this->parseAmount($data['cash'] ?? 0),
            "transfer" => $this->parseAmount($data['transfer'] ?? 0),
            "food_money" => $this->parseAmount($data['food_money'] ?? 0),
            "tuition_months" => $data['tuition_months'] ?? [],      // ✅ ລຶບ json_encode
            "food_months" => $data['food_months'] ?? [],            // ✅ ລຶບ json_encode
            "discount_id" => $data['discount_id'] ?? null,
            "discount_amount" => $this->parseAmount($data['discount_amount'] ?? 0),
            "total_amount" => $this->parseAmount($data['total_amount'] ?? 0),
            "late_fee" => $this->parseAmount($data['late_fee'] ?? 0),
            "note" => $data['note'] ?? null,
            'received_by' => auth()->id(),
            "payment_status" => "pending",
            "image_paths" => $data['image_path'] ?? [],
        ];
    }

    /**
     * ✅ ປັບປຸງ Confirm and save payment
     */
    public function confirmPayment(): void
    {
        try {
            DB::beginTransaction();

            // ກວດສອບຂໍ້ມູນອີກຄັ້ງກ່ອນບັນທຶກ
            $this->validateBeforeSave();

            // Create payment record
            $payment = Payment::create([
                "student_id" => $this->pendingPaymentData['student_id'],
                "academic_year_id" => $this->pendingPaymentData['academic_year_id'],
                "payment_date" => $this->pendingPaymentData['payment_date'],
                "receipt_number" => $this->pendingPaymentData['receipt_number'],
                "cash" => $this->pendingPaymentData['cash'],
                "transfer" => $this->pendingPaymentData['transfer'],
                "food_money" => $this->pendingPaymentData['food_money'],
                "tuition_months" => $this->pendingPaymentData['tuition_months'],
                "food_months" => $this->pendingPaymentData['food_months'],
                "discount_id" => $this->pendingPaymentData['discount_id'],
                "discount_amount" => $this->pendingPaymentData['discount_amount'],
                "total_amount" => $this->pendingPaymentData['total_amount'],
                "late_fee" => $this->pendingPaymentData['late_fee'],
                "note" => $this->pendingPaymentData['note'],
                "received_by" => $this->pendingPaymentData['received_by'],
                "payment_status" => "pending", // ຕັ້ງເປັນ confirmed ທັນທີ
            ]);

            // ✅ ປັບປຸງການບັນທຶກຮູບພາບ
            $this->savePaymentImages($payment);

            DB::commit();

            // Close modal
            $this->showConfirmModal = false;
            $this->pendingPaymentData = [];

            // Reset form
            $this->resetForm();

            Notification::make()
                ->title('ສຳເລັດ!')
                ->body("ບັນທຶກການຊຳລະເງິນສຳເລັດແລ້ວ - ໃບບິນເລກທີ: {$payment->receipt_number}")
                ->success()
                ->send();

            // Redirect to view payment or continue
            $this->redirect(route('filament.admin.resources.payments.view', $payment->payment_id));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving payment: ' . $e->getMessage(), [
                'student_id' => $this->selectedStudent?->student_id,
                'payment_data' => $this->pendingPaymentData
            ]);

            Notification::make()
                ->title('ເກີດຂໍ້ຜິດພາດ')
                ->body('ບໍ່ສາມາດບັນທຶກການຊຳລະເງິນໄດ້: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ ບັນທຶກຮູບພາບການຊຳລະເງິນ (ໃຊ້ PaymentImage model)
     */
    private function savePaymentImages(Payment $payment): void
    {
        try {
            // ✅ ດຶງຮູບພາບຈາກ form data ຫຼື pending data
            $imagePaths = $this->pendingPaymentData['image_paths'] ??
                $this->form->getState()['image_path'] ?? [];

            if (empty($imagePaths)) {
                Log::info('No images to save', ['payment_id' => $payment->payment_id]);
                return;
            }

            $results = ['success' => 0, 'failed' => 0, 'errors' => []];

            foreach ($imagePaths as $index => $imagePath) {
                try {
                    // ✅ ໃຊ້ validation ຈາກ model
                    if (!$this->validateSingleImage($imagePath, $index + 1, $results)) {
                        continue;
                    }

                    // ✅ ສ້າງ PaymentImage ດ້ວຍ model
                    $paymentImage = PaymentImage::create([
                        'payment_id' => $payment->payment_id,
                        'image_path' => $imagePath,
                        'image_type' => $this->determineImageType($imagePath),
                        'upload_date' => now(),
                    ]);

                    $results['success']++;

                    Log::info('Image saved', [
                        'payment_id' => $payment->payment_id,
                        'image_id' => $paymentImage->image_id,
                        'type' => $paymentImage->image_type
                    ]);

                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "ຮູບທີ່ " . ($index + 1) . ": " . $e->getMessage();
                    Log::error('Failed to save image', ['error' => $e->getMessage()]);
                }
            }

            // ✅ ສົ່ງ notification ຕາມຜົນ
            $this->notifyImageResults($results);

        } catch (\Exception $e) {
            Log::error('Critical error in savePaymentImages: ' . $e->getMessage());

            Notification::make()
                ->title('ບັນຫາການບັນທຶກຮູບພາບ')
                ->body('ກະລຸນາລອງໃໝ່ ຫຼື ຕິດຕໍ່ຜູ້ດູແລ')
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ ກວດສອບຮູບພາບແຕ່ລະຮູບ
     */
    private function validateSingleImage(string $imagePath, int $imageNumber, array &$results): bool
    {
        if (empty($imagePath)) {
            $results['failed']++;
            $results['errors'][] = "ຮູບທີ່ {$imageNumber}: ບໍ່ມີຂໍ້ມູນ";
            return false;
        }

        if (!Storage::disk('public')->exists($imagePath)) {
            $results['failed']++;
            $results['errors'][] = "ຮູບທີ່ {$imageNumber}: ບໍ່ພົບໄຟລ໌";
            return false;
        }

        // ✅ ກວດສອບຂະໜາດ (5MB)
        $fileSize = Storage::disk('public')->size($imagePath);
        if ($fileSize > 5242880) {
            $results['failed']++;
            $results['errors'][] = "ຮູບທີ່ {$imageNumber}: ໄຟລ໌ໃຫຍ່ເກີນ 5MB";
            return false;
        }

        // ✅ ກວດສອບປະເພດໄຟລ໌
        $mimeType = Storage::disk('public')->mimeType($imagePath);
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (!in_array($mimeType, $allowedTypes)) {
            $results['failed']++;
            $results['errors'][] = "ຮູບທີ່ {$imageNumber}: ປະເພດໄຟລ໌ບໍ່ຖືກຕ້ອງ";
            return false;
        }

        return true;
    }

    /**
     * ✅ ກຳນົດປະເພດຮູບພາບ (ໃຊ້ຮ່ວມກັບ PaymentImage model)
     */
    private function determineImageType(string $imagePath): string
    {
        $fileName = strtolower(basename($imagePath));

        if (str_contains($fileName, 'transfer') || str_contains($fileName, 'ໂອນ')) {
            return 'transfer_slip';
        }

        if (str_contains($fileName, 'receipt') || str_contains($fileName, 'ບິນ')) {
            return 'receipt';
        }

        return 'receipt'; // default
    }

    /**
     * ✅ ສົ່ງ notification ຕາມຜົນ
     */
    private function notifyImageResults(array $results): void
    {
        $total = $results['success'] + $results['failed'];

        if ($results['failed'] === 0 && $results['success'] > 0) {
            Notification::make()
                ->title('ບັນທຶກຮູບພາບສຳເລັດ')
                ->body("ບັນທຶກຮູບພາບທັງໝົດ {$results['success']} ຮູບສຳເລັດ")
                ->success()
                ->send();

        } elseif ($results['success'] > 0 && $results['failed'] > 0) {
            $errorMsg = implode(', ', array_slice($results['errors'], 0, 2));

            Notification::make()
                ->title('ບັນທຶກບາງສ່ວນ')
                ->body("ສຳເລັດ: {$results['success']}, ລົ້ມແຫຼວ: {$results['failed']}. {$errorMsg}")
                ->warning()
                ->send();

        } elseif ($results['failed'] > 0) {
            $errorMsg = implode(', ', array_slice($results['errors'], 0, 2));

            Notification::make()
                ->title('ບັນທຶກຮູບພາບລົ້ມແຫຼວ')
                ->body($errorMsg)
                ->danger()
                ->send();
        }
    }

    /**
     * ✅ ເພີ່ມການກວດສອບກ່ອນບັນທຶກ
     */
    private function validateBeforeSave(): void
    {
        if (!$this->selectedStudent) {
            throw new \Exception('ບໍ່ພົບຂໍ້ມູນນັກຮຽນ');
        }

        if (empty($this->pendingPaymentData['receipt_number'])) {
            throw new \Exception('ບໍ່ມີເລກໃບບິນ');
        }

        // ກວດສອບເລກໃບບິນຊ້ຳ
        $existingPayment = Payment::where('receipt_number', $this->pendingPaymentData['receipt_number'])
            ->first();
        if ($existingPayment) {
            throw new \Exception('ເລກໃບບິນນີ້ມີແລ້ວ ກະລຸນາສ້າງໃໝ່');
        }

        // ✅ ກວດສອບເດືອນຊ້ຳອີກຄັ້ງ - ແກ້ໄຂ
        $tuitionMonths = $this->pendingPaymentData['tuition_months'] ?? [];
        if (!empty($tuitionMonths)) {
            $paidTuitionMonths = Payment::getPaidTuitionMonths(
                $this->selectedStudent->student_id,
                $this->pendingPaymentData['academic_year_id']
            );
            $duplicateTuition = array_intersect($tuitionMonths, $paidTuitionMonths);
            if (!empty($duplicateTuition)) {
                // ✅ ແປງເປັນຊື່ເດືອນ
                $monthNames = array_map(fn($month) => Payment::getMonthName($month), $duplicateTuition);
                throw new \Exception('ເດືອນຄ່າຮຽນ ' . implode(', ', $monthNames) . ' ໄດ້ຈ່າຍແລ້ວ');
            }
        }

        $foodMonths = $this->pendingPaymentData['food_months'] ?? [];
        if (!empty($foodMonths)) {
            $paidFoodMonths = Payment::getPaidFoodMonths(
                $this->selectedStudent->student_id,
                $this->pendingPaymentData['academic_year_id']
            );
            $duplicateFood = array_intersect($foodMonths, $paidFoodMonths);
            if (!empty($duplicateFood)) {
                // ✅ ແປງເປັນຊື່ເດືອນ
                $monthNames = array_map(fn($month) => Payment::getMonthName($month), $duplicateFood);
                throw new \Exception('ເດືອນຄ່າອາຫານ ' . implode(', ', $monthNames) . ' ໄດ້ຈ່າຍແລ້ວ');
            }
        }
    }

    /**
     * Cancel payment confirmation
     */
    public function cancelPayment(): void
    {
        $this->showConfirmModal = false;
        $this->pendingPaymentData = [];

        Notification::make()
            ->title('ຍົກເລີກ')
            ->body('ຍົກເລີກການຊຳລະເງິນແລ້ວ')
            ->warning()
            ->send();
    }

    /**
     * ✅ ປັບປຸງ Reset form after successful payment
     */
    private function resetForm(): void
    {
        $receiptNo = Payment::generateReceiptNumber();
        $this->form->fill([
            "receipt_number" => $receiptNo,
            "receipt_number_view" => $receiptNo,
            'cash' => 0,
            'transfer' => 0,
            'payment_date' => now(),
            'late_fee' => 0,
            'food_money' => 0,
            'tuition_months' => [],
            'food_months' => [],
            'discount_id' => null,
            'discount_amount' => 0,
            'discount_amount_view' => '0',
            'total_amount' => 0,
            'total_amount_view' => '0',
            'note' => null,
            'image_path' => []
        ]);
    }

    /**
     * ✅ ເພີ່ມ method ສຳລັບ reset student selection
     */
    public function resetStudentSelection(): void
    {
        $this->selectedStudent = null;
        $this->profile_image = null;
        $this->search_val = '';
        $this->foundStudents = collect();
        $this->paidTuitionMonths = []; // ✅ ເພີ່ມ
        $this->paidFoodMonths = [];    // ✅ ເພີ່ມ
        $this->resetForm();

        Notification::make()
            ->title('ລ້າງຂໍ້ມູນແລ້ວ')
            ->body('ສາມາດເລືອກນັກຮຽນໃໝ່ໄດ້')
            ->info()
            ->send();
    }

    /**
     * ✅ ເພີ່ມ method ສຳລັບການຄິດໄລ່ລ່ວງໜ້າ
     */
    public function previewPayment(): array
    {
        $data = $this->form->getState();

        $cash = $this->parseAmount($data['cash'] ?? 0);
        $transfer = $this->parseAmount($data['transfer'] ?? 0);
        $foodMoney = $this->parseAmount($data['food_money'] ?? 0);
        $lateFee = $this->parseAmount($data['late_fee'] ?? 0);
        $discountId = $data['discount_id'] ?? null;

        $subtotal = $cash + $transfer + $foodMoney + $lateFee;
        $discountAmount = $this->calculateDiscountAmount($discountId, $subtotal);
        $total = max(0, $subtotal - $discountAmount);

        return [
            'cash' => $cash,
            'transfer' => $transfer,
            'food_money' => $foodMoney,
            'late_fee' => $lateFee,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'tuition_months' => $data['tuition_months'] ?? [],
            'food_months' => $data['food_months'] ?? [],
        ];
    }

    /**
     * ✅ ເພີ່ມ method ສຳລັບການຄົ້ນຫາປະຫວັດການຊຳລະ
     */
    /**
     * ✅ ດຶງປະຫວັດການຊຳລະເດືອນຂອງນັກຮຽນ
     */
    public function loadPaymentHistory(): void
    {
        if (!$this->selectedStudent || !$this->currentAcademicYear) {
            $this->paidTuitionMonths = [];
            $this->paidFoodMonths = [];
            return;
        }

        try {
            // ດຶງເດືອນທີ່ຈ່າຍແລ້ວ
            $this->paidTuitionMonths = Payment::getPaidTuitionMonths(
                $this->selectedStudent->student_id,
                $this->currentAcademicYear->academic_year_id
            );

            $this->paidFoodMonths = Payment::getPaidFoodMonths(
                $this->selectedStudent->student_id,
                $this->currentAcademicYear->academic_year_id
            );

            // ✅ Refresh table
            $this->resetTable();

        } catch (\Exception $e) {
            Log::error('Error loading payment history: ' . $e->getMessage());
            $this->paidTuitionMonths = [];
            $this->paidFoodMonths = [];
        }
    }

    /**
     * ✅ ເພີ່ມ method ສຳລັບການສ້າງໃບບິນໃໝ່
     */
    public function generateNewReceiptNumber(): void
    {
        $newReceiptNo = Payment::generateReceiptNumber();
        $this->form->fill([
            'receipt_number' => $newReceiptNo,
            'receipt_number_view' => $newReceiptNo,
        ]);

        Notification::make()
            ->title('ສ້າງເລກໃບບິນໃໝ່')
            ->body("ເລກໃບບິນໃໝ່: {$newReceiptNo}")
            ->success()
            ->send();
    }

    /**
     * ✅ ເພີ່ມ method ສຳລັບການກວດສອບສຸຂະພາບລະບົບ
     */
    public function checkSystemHealth(): array
    {
        $health = [
            'database' => false,
            'storage' => false,
            'academic_year' => false,
        ];

        try {
            // ກວດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
            DB::connection()->getPdo();
            $health['database'] = true;

            // ກວດສອບ storage
            if (Storage::disk('public')->exists('')) {
                $health['storage'] = true;
            }

            // ກວດສອບສົກຮຽນປັດຈຸບັນ
            if ($this->currentAcademicYear) {
                $health['academic_year'] = true;
            }

        } catch (\Exception $e) {
            Log::error('System health check failed: ' . $e->getMessage());
        }

        return $health;
    }

}