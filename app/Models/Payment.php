<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
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
        'cash' => 'decimal:2',
        'transfer' => 'decimal:2',
        'food_money' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // ❌ ເອົາ cast JSON ອອກ ເພື່ອໃຊ້ Attribute ແທນ
        // 'tuition_months' => 'array',
        // 'food_months' => 'array',
    ];

    /**
     * Relationships
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PaymentImage::class, 'payment_id');
    }

    /**
     * ✅ ແກ້ໄຂ Accessors & Mutators
     */
    protected function paymentDate(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value) : null,
            set: fn($value) => $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null,
        );
    }

    protected function tuitionMonths(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return [];
                }

                // ຖ້າເປັນ string (JSON) ແລ້ວ
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : [];
                }

                // ຖ້າເປັນ array ແລ້ວ
                if (is_array($value)) {
                    return $value;
                }

                return [];
            },
            set: function ($value) {
                if (empty($value)) {
                    return json_encode([]);
                }

                // ຖ້າເປັນ string (JSON) ແລ້ວ - ຕົວເລືອກ 1: ສົ່ງຜ່ານໄປເລີຍ
                if (is_string($value)) {
                    // ກວດສອບວ່າເປັນ JSON ທີ່ຖືກຕ້ອງຫຼືບໍ່
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return json_encode(array_values($decoded));
                    }
                    // ຖ້າບໍ່ແມ່ນ JSON ທີ່ຖືກຕ້ອງ, ເອົາມາເປັນ array ເດືອນດຽວ
                    return json_encode([$value]);
                }

                // ຖ້າເປັນ array
                if (is_array($value)) {
                    return json_encode(array_values($value));
                }

                // ຖ້າເປັນອີກປະເພດນຶ່ງ (null, number, etc.)
                return json_encode([]);
            }
        );
    }

    protected function foodMonths(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return [];
                }

                // ຖ້າເປັນ string (JSON) ແລ້ວ
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : [];
                }

                // ຖ້າເປັນ array ແລ້ວ
                if (is_array($value)) {
                    return $value;
                }

                return [];
            },
            set: function ($value) {
                if (empty($value)) {
                    return null; // ສາມາດເປັນ null ໄດ້
                }

                // ຖ້າເປັນ string (JSON) ແລ້ວ
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return json_encode(array_values($decoded));
                    }
                    return json_encode([$value]);
                }

                // ຖ້າເປັນ array
                if (is_array($value)) {
                    return json_encode(array_values($value));
                }

                return null;
            }
        );
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
    public function getTotalCashAndTransfer(): float
    {
        return (float) ($this->cash + $this->transfer);
    }

    public function getTuitionMonthsCount(): int
    {
        return count($this->tuition_months ?? []);
    }

    public function getFoodMonthsCount(): int
    {
        return count($this->food_months ?? []);
    }

    public function getTuitionMonthsFormatted(): string
    {
        return implode(', ', $this->tuition_months ?? []);
    }

    public function getFoodMonthsFormatted(): string
    {
        return implode(', ', $this->food_months ?? []) ?: 'ບໍ່ມີ';
    }

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

    public function getFormattedTotal(): string
    {
        return number_format($this->total_amount, 0, '.', ',') . ' ກີບ';
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
     * Static Methods
     */
    public static function getMonthOptions(): array
    {
        return [
            'ມັງກອນ' => 'ມັງກອນ',
            'ກຸມພາ' => 'ກຸມພາ',
            'ມີນາ' => 'ມີນາ',
            'ເມສາ' => 'ເມສາ',
            'ພຶດສະພາ' => 'ພຶດສະພາ',
            'ມິຖຸນາ' => 'ມິຖຸນາ',
            'ກໍລະກົດ' => 'ກໍລະກົດ',
            'ສິງຫາ' => 'ສິງຫາ',
            'ກັນຍາ' => 'ກັນຍາ',
            'ຕຸລາ' => 'ຕຸລາ',
            'ພະຈິກ' => 'ພະຈິກ',
            'ທັນວາ' => 'ທັນວາ',
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'ລໍຖ້າຢືນຢັນ',
            'confirmed' => 'ຢືນຢັນແລ້ວ',
            'cancelled' => 'ຍົກເລີກ',
            'refunded' => 'ຄືນເງິນ',
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
     * Validation Methods
     */
    public function hasValidTuitionMonths(): bool
    {
        return !empty($this->tuition_months) && is_array($this->tuition_months);
    }

    public function hasValidAmount(): bool
    {
        return $this->total_amount > 0;
    }

    public function hasValidPaymentMethods(): bool
    {
        return ($this->cash + $this->transfer + $this->food_money) > 0;
    }

    /**
     * ✅ ແກ້ໄຂ Validation ສຳລັບການກວດສອບເດືອນຊ້ຳ
     */
    public static function getPaidTuitionMonths($studentId, $academicYearId = null): array
    {
        $query = static::where('student_id', $studentId)
            ->where('payment_status', '!=', 'cancelled')
            ->whereNotNull('tuition_months');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $payments = $query->get();

        $paidMonths = [];
        foreach ($payments as $payment) {
            $months = $payment->tuition_months; // ໃຊ້ accessor
            if (is_array($months)) {
                $paidMonths = array_merge($paidMonths, $months);
            }
        }

        return array_unique($paidMonths);
    }

    public static function getPaidFoodMonths($studentId, $academicYearId = null): array
    {
        $query = static::where('student_id', $studentId)
            ->where('payment_status', '!=', 'cancelled')
            ->whereNotNull('food_months');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $payments = $query->get();

        $paidMonths = [];
        foreach ($payments as $payment) {
            $months = $payment->food_months; // ໃຊ້ accessor
            if (is_array($months)) {
                $paidMonths = array_merge($paidMonths, $months);
            }
        }

        return array_unique($paidMonths);
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }

            if (empty($payment->received_by)) {
                $payment->received_by = auth()->id();
            }
        });

        static::updating(function ($payment) {
            // Log changes if needed
            if ($payment->isDirty('payment_status')) {
                // Add audit log here
            }
        });
    }
}