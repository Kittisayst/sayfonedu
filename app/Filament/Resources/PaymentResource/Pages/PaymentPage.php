<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentImage;
use App\Models\Student;
use App\Models\Discount;
use App\Utils\Money;
use App\Utils\CalculatePay;
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

class PaymentPage extends Page implements HasForms
{
    use InteractsWithForms;

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

    public $pendingPaymentData = [];
    public $showConfirmModal = false;

    /**
     * ເລີ່ມຕົ້ນເມື່ອໂຫຼດໜ້າ
     */
    public function mount(): void
    {
        $this->foundStudents = collect();

        // ຕັ້ງຄ່າ default values
        $receiptNo = 'PAY-' . now()->format('YmdHis');
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
            'discount_id' => 0,
            'total_amount' => 0
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // ເດືອນຄ່າຮຽນ
                Grid::make(2)
                    ->schema([
                        Fieldset::make("tuition_months_section")
                            ->label('ເດືອນຄ່າຮຽນ')
                            ->schema([
                                CheckboxList::make('tuition_months')
                                    ->hiddenLabel()
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->required()
                                    ->live()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                if (empty($value)) {
                                                    $fail('ກະລຸນາເລືອກຢ່າງໜ້ອຍ 1 ເດືອນ');
                                                }

                                                // ກວດສອບວ່າເດືອນນີ້ຈ່າຍແລ້ວຫຼືຍັງ
                                                if ($this->selectedStudent && !empty($value)) {
                                                    $paidMonths = $this->getPaidTuitionMonths($this->selectedStudent->student_id);
                                                    $duplicates = array_intersect($value, $paidMonths);
                                                    if (!empty($duplicates)) {
                                                        $fail('ເດືອນ ' . implode(', ', $duplicates) . ' ໄດ້ຈ່າຍແລ້ວ');
                                                    }
                                                }
                                            };
                                        },
                                    ])
                            ])->columnSpan(1),

                        // ເດືອນຄ່າອາຫານ
                        Fieldset::make("food_months_section")
                            ->label('ເດືອນຄ່າອາຫານ')
                            ->schema([
                                CheckboxList::make('food_months')
                                    ->hiddenLabel()
                                    ->options(Payment::getMonthOptions())
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                // ກວດສອບວ່າເດືອນນີ້ຈ່າຍແລ້ວຫຼືຍັງ
                                                if ($this->selectedStudent && !empty($value)) {
                                                    $paidMonths = $this->getPaidFoodMonths($this->selectedStudent->student_id);
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
                // ຄ່າທຳນຽມ
                Grid::make(2)
                    ->schema([
                        Fieldset::make("cash_money")
                            ->label('ເງິນສົດ')
                            ->schema([
                                TextInput::make('cash')
                                    ->label('ຈຳນວນເງິນສົດ')
                                    ->prefixIcon('heroicon-o-banknotes')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('ປ່ອນຈຳນວນເງິນ')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make("food_money")
                                    ->label('ຄ່າອາຫານ')
                                    ->prefixIcon('heroicon-o-cube')
                                    ->numeric()
                                    ->minValue(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                Select::make('discount_id')
                                    ->label("ສ່ວນຫຼຸດ")
                                    ->prefixIcon('heroicon-o-gift')
                                    ->placeholder("ເລືອກສ່ວນຫຼຸດ")
                                    ->options(Discount::getSelectOptions())
                                    ->live()
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                TextInput::make("late_fee")
                                    ->label('ຄ່າຈ່າຍຊ້າ')
                                    ->prefixIcon('heroicon-o-flag')
                                    ->numeric()
                                    ->minValue(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),
                            ])
                            ->columns(1)
                            ->columnSpan(1),

                        Fieldset::make("transfer_money")
                            ->label('ເງິນໂອນ')
                            ->schema([
                                TextInput::make('transfer')
                                    ->label('ຈຳນວນເງິນໂອນ')
                                    ->prefixIcon('heroicon-o-credit-card')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('ປ່ອນຈຳນວນເງິນ')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),

                                DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->placeholder('ເລືອກວັນທີ')
                                    ->required()
                                    ->maxDate(now()->addDay()),

                                FileUpload::make('image_path')
                                    ->label("ຮູບການໂອນ")
                                    ->disk('public')
                                    ->directory('payment_receipts')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                                    ->maxSize(5120) // 5MB
                                    ->imagePreviewHeight('128')
                                    ->multiple()
                                    ->maxFiles(3)
                                    ->deleteUploadedFileUsing(function ($file) {
                                        Storage::disk('public')->delete($file);
                                    }),
                            ])
                            ->columns(1)
                            ->columnSpan(1),
                    ]),

                Fieldset::make('ຈຳນວນເງິນທັງໝົດ')
                    ->schema([
                        TextInput::make('receipt_number_view')
                            ->label('ເລກໃບຮັບເງິນ')
                            ->prefixIcon('heroicon-o-hashtag')
                            ->placeholder('ປ້ອນເລກໃບຮັບເງິນ')
                            ->disabled(),

                        TextInput::make('total_amount_view')
                            ->label("ລວມເງິນທັງໝົດ")
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->disabled()
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),

                        TextInput::make('discount_amount_view')
                            ->label('ສ່ວນຫຼຸດ')
                            ->prefixIcon('heroicon-o-tag')
                            ->disabled()
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),

                        Hidden::make('receipt_number'),
                        Hidden::make('discount_amount'),
                        Hidden::make('total_amount'),
                    ]),

                Textarea::make('note')
                    ->label("ໝາຍເຫດ")
                    ->rows(3)
                    ->maxLength(500)
            ])
            ->statePath('data');
    }

    /**
     * ຄິດໄລ່ລວມເງິນ
     */
    private function calculateTotal(Set $set, Get $get): void
    {
        $cash = Money::toInt($get('cash') ?? 0);
        $transfer = Money::toInt($get('transfer') ?? 0);
        $latefee = Money::toInt($get('late_fee') ?? 0);
        $discountId = $get('discount_id');

        $sum = CalculatePay::Amount($cash, $transfer, $latefee);
        $discountAmount = CalculatePay::DiscountAmount($discountId, $sum);

        $total = $sum - $discountAmount;

        $set('discount_amount_view', Money::toLAK($discountAmount));
        $set('total_amount_view', Money::toLAK($total));
        $set('discount_amount', $discountAmount);
        $set('total_amount', $total);
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
     * Method ສຳລັບ Header Action
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
     * Validate payment data
     */
    private function validatePaymentData(array $data): void
    {
        // Check if at least one payment type is entered
        $cash = Money::toInt($data['cash'] ?? 0);
        $transfer = Money::toInt($data['transfer'] ?? 0);
        $foodMoney = Money::toInt($data['food_money'] ?? 0);

        if ($cash == 0 && $transfer == 0 && $foodMoney == 0) {
            throw new \Exception('ກະລຸນາປ້ອນຈຳນວນເງິນທີ່ຈະຊຳລະ');
        }

        // Check total amount
        if (($data['total_amount'] ?? 0) <= 0) {
            throw new \Exception('ຈຳນວນເງິນລວມຕ້ອງມາກກວ່າ 0');
        }

        // Validate months
        if (empty($data['tuition_months']) && empty($data['food_months'])) {
            throw new \Exception('ກະລຸນາເລືອກເດືອນທີ່ຈະຊຳລະ');
        }
    }

    /**
     * Prepare payment data for confirmation
     */
    private function preparePaymentData(array $data): void
    {
        $this->pendingPaymentData = [
            "student_id" => $this->selectedStudent->student_id,
            "academic_year_id" => 1, // ຄວນດຶງຈາກການຕັ້ງຄ່າລະບົບ
            "payment_date" => $data['payment_date'] ?? now(),
            "receipt_number" => $data["receipt_number"] ?? 'PAY-' . now()->format('YmdHis'),
            "cash" => Money::toInt($data['cash'] ?? 0),
            "transfer" => Money::toInt($data['transfer'] ?? 0),
            "food_money" => Money::toInt($data['food_money'] ?? 0),
            "tuition_months" => json_encode($data['tuition_months'] ?? []),
            "food_months" => json_encode($data['food_months'] ?? []),
            "discount_id" => $data['discount_id'] ?? null,
            "discount_amount" => $data['discount_amount'] ?? 0,
            "total_amount" => $data['total_amount'] ?? 0,
            "late_fee" => Money::toInt($data['late_fee'] ?? 0),
            "note" => $data['note'] ?? null,
            'received_by' => auth()->id(),
            "payment_status" => "pending",
            "image_paths" => $data['image_path'] ?? [],
        ];
    }

    /**
     * Confirm and save payment
     */
    public function confirmPayment(): void
    {
        try {
            DB::beginTransaction();

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
                "payment_status" => "confirmed",
            ]);

            // Save payment images if any
            if (!empty($this->pendingPaymentData['image_paths'])) {
                foreach ($this->pendingPaymentData['image_paths'] as $imagePath) {
                    PaymentImage::create([
                        'payment_id' => $payment->payment_id,
                        'image_path' => $imagePath,
                        'upload_date' => now()
                    ]);
                }
            }

            DB::commit();

            // Close modal
            $this->showConfirmModal = false;
            $this->pendingPaymentData = [];

            // Reset form
            $this->resetForm();

            Notification::make()
                ->title('ສຳເລັດ!')
                ->body('ບັນທຶກການຊຳລະເງິນສຳເລັດແລ້ວ')
                ->success()
                ->send();

            // Redirect to print receipt or payment list
            $this->redirect(route('filament.admin.resources.payments.index'));

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->title('ເກີດຂໍ້ຜິດພາດ')
                ->body('ບໍ່ສາມາດບັນທຶກການຊຳລະເງິນໄດ້: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Cancel payment confirmation
     */
    public function cancelPayment(): void
    {
        $this->showConfirmModal = false;
        $this->pendingPaymentData = [];
    }

    /**
     * Reset form after successful payment
     */
    private function resetForm(): void
    {
        $receiptNo = 'PAY-' . now()->format('YmdHis');
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
            'discount_id' => 0,
            'total_amount' => 0,
            'note' => null,
            'image_path' => []
        ]);
    }

    /**
     * Get paid tuition months for validation
     */
    private function getPaidTuitionMonths(int $studentId): array
    {
        return Payment::getPaidTuitionMonths($studentId, 1); // 1 = current academic year
    }

    /**
     * Get paid food months for validation
     */
    private function getPaidFoodMonths(int $studentId): array
    {
        return Payment::getPaidFoodMonths($studentId, 1); // 1 = current academic year
    }
}