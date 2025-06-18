<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    /**
     * Primary key ຕາມໂຄງສ້າງຖານຂໍ້ມູນ
     */
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'receipt_number',
        'payment_date',
        'cash',
        'transfer',
        'food_money',
        'tuition_months',
        'food_months',
        'image_path',
        'discount_id',
        'discount_amount',
        'late_fee',
        'total_amount',
        'note',
        'received_by',
        'payment_status',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'cash' => 'integer',
        'transfer' => 'integer',
        'food_money' => 'integer',
        'discount_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tuition_months' => 'array',
        'food_months' => 'array',
    ];

    /**
     * Relationships
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'discount_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by', 'user_id');
    }

    /**
     * ຂໍ້ມູນເດືອນ
     */
    public static function getMonthOptions(): array
    {
        return [
            '1' => 'ມັງກອນ',
            '2' => 'ກຸມພາ',
            '3' => 'ມີນາ',
            '4' => 'ເມສາ',
            '5' => 'ພຶດສະພາ',
            '6' => 'ມິຖຸນາ',
            '7' => 'ກໍລະກົດ',
            '8' => 'ສິງຫາ',
            '9' => 'ກັນຍາ',
            '10' => 'ຕຸລາ',
            '11' => 'ພະຈິກ',
            '12' => 'ທັນວາ',
        ];
    }

    public static function getMonthName(string $monthNumber): string
    {
        $months = self::getMonthOptions();
        return $months[$monthNumber] ?? "ເດືອນທີ {$monthNumber}";
    }

    public static function getMonthNumber(string $monthName): ?string
    {
        $months = array_flip(self::getMonthOptions());
        return $months[$monthName] ?? null;
    }

    /**
     * ດຶງຊື່ເດືອນຄ່າຮຽນ
     */
    public function getTuitionMonthNames(): array
    {
        $months = $this->tuition_months ?? [];
        return array_map(function ($month) {
            return self::getMonthName((string) $month);
        }, $months);
    }

    /**
     * ດຶງຊື່ເດືອນຄ່າອາຫານ
     */
    public function getFoodMonthNames(): array
    {
        $months = $this->food_months ?? [];
        return array_map(function ($month) {
            return self::getMonthName((string) $month);
        }, $months);
    }

    public function getTuitionMonthsAsNumbers(): string
    {
        $months = $this->getSortedTuitionMonths();
        if (empty($months)) {
            return 'ບໍ່ມີ';
        }
        return implode(', ', $months);
    }

    public function getFoodMonthsAsNumbers(): string
    {
        $months = $this->getSortedFoodMonths();
        if (empty($months)) {
            return 'ບໍ່ມີ';
        }
        return implode(', ', $months);
    }

    /**
     * Sort ເດືອນຕາມລຳດັບ
     */
    public function getSortedTuitionMonths(): array
    {
        $months = $this->tuition_months ?? [];
        $monthNumbers = array_map(fn($month) => (int) $month, $months);
        sort($monthNumbers);
        return $monthNumbers;
    }

    public function getSortedFoodMonths(): array
    {
        $months = $this->food_months ?? [];
        $monthNumbers = array_map(fn($month) => (int) $month, $months);
        sort($monthNumbers);
        return $monthNumbers;
    }

    /**
     * ສະແດງຜົນເດືອນທີ່ຈ່າຍ
     */
    public function getTuitionMonthsDisplay(): string
    {
        $months = $this->getSortedTuitionMonths();
        if (empty($months)) {
            return 'ບໍ່ມີ';
        }
        $monthNames = array_map(fn($num) => self::getMonthName((string) $num), $months);
        return implode(', ', $monthNames);
    }

    public function getFoodMonthsDisplay(): string
    {
        $months = $this->getSortedFoodMonths();
        if (empty($months)) {
            return 'ບໍ່ມີ';
        }
        $monthNames = array_map(fn($num) => self::getMonthName((string) $num), $months);
        return implode(', ', $monthNames);
    }

    /**
     * ກວດສອບວ່າຈ່າຍເດືອນໃດແລ້ວ
     */
    public function hasPaidMonth(string $monthNumber, string $type = 'tuition'): bool
    {
        $months = $type === 'tuition' ? $this->tuition_months : $this->food_months;
        if (empty($months)) {
            return false;
        }
        return in_array($monthNumber, $months);
    }

    /**
     * Scopes
     */
    public function scopeConfirmed($query)
    {
        return $query->where('payment_status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('payment_status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeInDateRange($query, $from, $to)
    {
        return $query->whereBetween('payment_date', [$from, $to]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }

    /**
     * Helper Methods
     */
    public function getTotalCashAndTransfer(): int
    {
        return (int) ($this->cash + $this->transfer);
    }

    public function getTuitionMonthsCount(): int
    {
        $months = $this->tuition_months;
        return is_array($months) ? count($months) : 0;
    }

    public function getFoodMonthsCount(): int
    {
        $months = $this->food_months;
        return is_array($months) ? count($months) : 0;
    }

    public function getTuitionMonthsFormatted(): string
    {
        return $this->getTuitionMonthsDisplay();
    }

    public function getFoodMonthsFormatted(): string
    {
        return $this->getFoodMonthsDisplay();
    }

    /**
     * ກວດສອບຄວາມຖືກຕ້ອງຂອງຂໍ້ມູນ
     */
    public function hasValidTuitionMonths(): bool
    {
        $months = $this->tuition_months;
        return is_array($months) && !empty($months);
    }

    public function hasValidFoodMonths(): bool
    {
        $months = $this->food_months;
        return is_array($months) && !empty($months);
    }

    public function getTuitionMonthsSafe(): array
    {
        try {
            $months = $this->tuition_months;
            return is_array($months) ? $months : [];
        } catch (\Exception $e) {
            Log::error('Error getting tuition months: ' . $e->getMessage());
            return [];
        }
    }

    public function getFoodMonthsSafe(): array
    {
        try {
            $months = $this->food_months;
            return is_array($months) ? $months : [];
        } catch (\Exception $e) {
            Log::error('Error getting food months: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ດຶງເດືອນທີ່ຈ່າຍແລ້ວຂອງນັກຮຽນ
     */
    public static function getPaidTuitionMonths($studentId, $academicYearId = null, $excludePaymentId = null): array
    {
        try {
            $query = static::where('student_id', $studentId)
                ->whereIn('payment_status', ['confirmed', 'pending'])
                ->whereNotNull('tuition_months')
                ->where('tuition_months', '!=', '')
                ->where('tuition_months', '!=', '[]');

            if ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }

            if ($excludePaymentId) {
                $query->where('payment_id', '!=', $excludePaymentId);
            }

            $payments = $query->get();
            $paidMonths = [];

            foreach ($payments as $payment) {
                $months = $payment->getTuitionMonthsSafe();
                if (!empty($months)) {
                    $paidMonths = array_merge($paidMonths, $months);
                }
            }

            return array_unique($paidMonths);
        } catch (\Exception $e) {
            Log::error('Error getting paid tuition months: ' . $e->getMessage());
            return [];
        }
    }

    public static function getPaidFoodMonths($studentId, $academicYearId = null, $excludePaymentId = null): array
    {
        try {
            $query = static::where('student_id', $studentId)
                ->whereIn('payment_status', ['confirmed', 'pending'])
                ->whereNotNull('food_months')
                ->where('food_months', '!=', '')
                ->where('food_months', '!=', '[]');

            if ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }

            if ($excludePaymentId) {
                $query->where('payment_id', '!=', $excludePaymentId);
            }

            $payments = $query->get();
            $paidMonths = [];

            foreach ($payments as $payment) {
                $months = $payment->getFoodMonthsSafe();
                if (!empty($months)) {
                    $paidMonths = array_merge($paidMonths, $months);
                }
            }

            return array_unique($paidMonths);
        } catch (\Exception $e) {
            Log::error('Error getting paid food months: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ກວດສອບເດືອນຊ້ຳ
     */
    public function hasDuplicateTuitionMonths(array $newMonths): array
    {
        $existingMonths = static::getPaidTuitionMonths(
            $this->student_id,
            $this->academic_year_id,
            $this->payment_id
        );

        return array_intersect($newMonths, $existingMonths);
    }

    public function hasDuplicateFoodMonths(array $newMonths): array
    {
        $existingMonths = static::getPaidFoodMonths(
            $this->student_id,
            $this->academic_year_id,
            $this->payment_id
        );

        return array_intersect($newMonths, $existingMonths);
    }

    /**
     * Status checking methods
     */
    public function isConfirmed(): bool
    {
        return $this->payment_status === 'confirmed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isCancelled(): bool
    {
        return $this->payment_status === 'cancelled';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    public function canBeEdited(): bool
    {
        return $this->isPending() || auth()->user()?->hasRole('admin');
    }

    public function canBeDeleted(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->payment_status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'info',
            default => 'gray',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->payment_status) {
            'pending' => 'ລໍຖ້າຢືນຢັນ',
            'confirmed' => 'ຢືນຢັນແລ້ວ',
            'cancelled' => 'ຍົກເລີກ',
            'refunded' => 'ຄືນເງິນ',
            default => $this->payment_status,
        };
    }

    /**
     * Formatting methods
     */
    public function getFormattedTotal(): string
    {
        return number_format($this->total_amount, 0, '.', ',') . ' ກີບ';
    }

    public function getFormattedCash(): string
    {
        return number_format($this->cash, 0, '.', ',') . ' ກີບ';
    }

    public function getFormattedTransfer(): string
    {
        return number_format($this->transfer, 0, '.', ',') . ' ກີບ';
    }

    public function getFormattedFoodMoney(): string
    {
        return number_format($this->food_money, 0, '.', ',') . ' ກີບ';
    }

    public function getFormattedDate(): string
    {
        return $this->payment_date?->format('d/m/Y H:i') ?? '';
    }

    public function getFormattedDateOnly(): string
    {
        return $this->payment_date?->format('d/m/Y') ?? '';
    }

    /**
     * Validation methods
     */
    public function hasValidAmount(): bool
    {
        return $this->total_amount > 0;
    }

    public function hasValidPaymentMethods(): bool
    {
        return ($this->cash + $this->transfer + $this->food_money) > 0;
    }

    /**
     * Calculate total payment by type
     */
    public function calculateTotalPayment(): int
    {
        return $this->cash + $this->transfer + $this->food_money;
    }

    /**
     * Static Methods
     */
    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'ລໍຖ້າຢືນຢັນ',
            'confirmed' => 'ຢືນຢັນແລ້ວ',
            'cancelled' => 'ຍົກເລີກ',
            'refunded' => 'ຄືນເງິນ',
        ];
    }

    public static function getPaymentMethodOptions(): array
    {
        return [
            'cash' => 'ເງິນສົດ',
            'transfer' => 'ໂອນເງິນ',
            'mixed' => 'ປະສົມ',
        ];
    }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $lastPayment = static::whereDate('created_at', today())
            ->orderBy('payment_id', 'desc')
            ->first();

        $sequence = $lastPayment ?
            intval(substr($lastPayment->receipt_number, -4)) + 1 : 1;

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Statistics Methods
     */
    public static function getTodayTotal(): float
    {
        return static::today()->confirmed()->sum('total_amount');
    }

    public static function getThisMonthTotal(): float
    {
        return static::thisMonth()->confirmed()->sum('total_amount');
    }

    public static function getThisYearTotal(): float
    {
        return static::thisYear()->confirmed()->sum('total_amount');
    }

    public static function getTodayCashTotal(): int
    {
        return static::today()->confirmed()->sum('cash');
    }

    public static function getTodayTransferTotal(): int
    {
        return static::today()->confirmed()->sum('transfer');
    }

    public static function getPendingCount(): int
    {
        return static::pending()->count();
    }

    public static function getTodayCount(): int
    {
        return static::today()->count();
    }

    public static function getMonthlyStats($year = null): array
    {
        $year = $year ?? now()->year;

        return static::selectRaw('MONTH(payment_date) as month, SUM(total_amount) as total, COUNT(*) as count')
            ->whereYear('payment_date', $year)
            ->confirmed()
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();
    }

    /**
     * Get payment summary by student
     */
    public static function getStudentPaymentSummary($studentId, $academicYearId = null): array
    {
        $query = static::where('student_id', $studentId)
            ->confirmed();

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $payments = $query->get();

        return [
            'total_amount' => $payments->sum('total_amount'),
            'total_cash' => $payments->sum('cash'),
            'total_transfer' => $payments->sum('transfer'),
            'total_food_money' => $payments->sum('food_money'),
            'payment_count' => $payments->count(),
            'tuition_months' => $payments->flatMap(fn($p) => $p->getTuitionMonthsSafe())->unique()->count(),
            'food_months' => $payments->flatMap(fn($p) => $p->getFoodMonthsSafe())->unique()->count(),
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // ສ້າງເລກໃບບິນອັດຕະໂນມັດ
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }

            // ກຳນົດຜູ້ຮັບເງິນ
            if (empty($payment->received_by)) {
                $payment->received_by = auth()->id();
            }

            // ກຳນົດ payment_date ຖ້າບໍ່ມີ
            if (empty($payment->payment_date)) {
                $payment->payment_date = now();
            }
        });

        static::updating(function ($payment) {
            // Log changes if needed
            if ($payment->isDirty('payment_status')) {
                Log::info('Payment status changed', [
                    'payment_id' => $payment->payment_id,
                    'old_status' => $payment->getOriginal('payment_status'),
                    'new_status' => $payment->payment_status,
                    'user_id' => auth()->id()
                ]);
            }
        });
    }
}