<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
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
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->placeholder('ປ່ອນຈຳນວນເງິນ')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn($state, Set $set, Get $get) => $this->calculateTotal($set, $get)),
                                DateTimePicker::make('payment_date')
                                    ->label('ວັນທີຊຳລະ')
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->placeholder('ເລືອກວັນທີ')
                                    ->required(),
                                FileUpload::make('image_path')
                                    ->label("ຮູບການໂອນ")
                                    ->disk('public') // ກຳນົດ disk ທີ່ໃຊ້ເກັບ
                                    ->directory('payment_receipts') // ກຳນົດ folder ໃນ disk
                                    ->visibility('public') // ກຳນົດໃຫ້ໄຟລ໌ສາມາດເຂົ້າເຖິງໄດ້ຈາກ URL
                                    ->acceptedFileTypes(['image/*']) // ອະນຸຍາດສະເພາະໄຟລ໌ຮູບພາບ
                                    ->imagePreviewHeight('128')
                                    ->deleteUploadedFileUsing(function ($file) {
                                        // Custom logic ສຳລັບລຶບໄຟລ໌
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
                            ->disabled() // ຫ້າມແກ້ໄຂດ້ວຍມື
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        TextInput::make('discount_amount_view')
                            ->label('ສ່ວນຫຼຸດ')
                            ->prefixIcon('heroicon-o-tag')
                            ->disabled() // ຫ້າມແກ້ໄຂດ້ວຍມື
                            ->numeric()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(','),
                        Hidden::make('receipt_number'),
                        Hidden::make('discount_amount'),
                        Hidden::make('total_amount'),
                    ]),
                Textarea::make('note')
                    ->label("ໝາຍເຫດ")
                    ->rows(5)
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
        // dd($total);
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
     * ⭐ Method ສຳລັບ Header Action
     */
    public function processPaymentAction(): void
    {
        try {

            $this->validate();
            $data = $this->form->getState();

            if (!$this->selectedStudent) {
                throw new \Exception('ກະລຸນາເລືອກນັກຮຽນກ່ອນ');
            }

            $this->savePayment($data);
        } catch (\Exception $e) {
            Notification::make()
                ->title('ເກີດຂໍ້ຜິດພາດ')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * ບັນທຶກຂໍ້ມູນການຊຳລະ
     */
    private function savePayment(array $data): void
    {
        // ສ້າງ Payment record
        $this->pendingPaymentData = [
            "student_id" => $this->selectedStudent->student_id,
            "academic_year_id" => 1,
            "payment_date" => now(),
            "receipt_number" => $data["receipt_number"] ?? 0,
            "cash" => $data['cash'],
            "transfer" => $data['transfer'],
            "food_money" => $data['food_money'],
            "tuition_months" => json_encode($data['tuition_months']),
            "food_months" => json_encode($data['food_months']),
            "discount_id" => $data['discount_id'] ?? 0,
            "discount_amount" => $data['discount_amount'] ?? 0,
            "total_amount" => $data['total_amount'] ?? 0,
            "late_fee" => $data['late_fee'] ?? 0,
            "note" => $data['note'] ?? null,
            'received_by' => auth()->id(),
            "payment_status" => "pending",
        ];

        // dd($saveData);

        // ສະແດງ confirmation modal
        $this->dispatch('open-modal', id: 'confirm-payment-modal');

        // $payment = Payment::create($saveData);

        // if (!empty($data['image_path'])) {
        //     // ຖ້າມີຫຼາຍຮູບ (array)
        //     if (is_array($data['image_path'])) {
        //         foreach ($data['image_path'] as $imagePath) {
        //             $payment->images()->create([
        //                 'image_path' => $imagePath,
        //                 'upload_date' => now()
        //             ]);
        //         }
        //     } else {
        //         // ຮູບດຽວ
        //         $payment->images()->create([
        //             'image_path' => $data['image_path'],
        //             'upload_date' => now()
        //         ]);
        //     }
        // }

        // Notification::make()
        //     ->title('ສຳເລັດ')
        //     ->body('ບັນທຶກການຊຳລະເງິນສຳເລັດແລ້ວ')
        //     ->success()
        //     ->send();
    }

    private function confirmPayment()
    {
        dd("hello");
    }
}
