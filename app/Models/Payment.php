<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'payment_date',
        'payment_method',
        'transaction_id',
        'receipt_number',
        'tuition_amount',
        'tuition_months',
        'food_amount',
        'food_months',
        'discount_id',
        'discount_amount',
        'late_fee',
        'total_amount',
        'note',
        'received_by',
        'is_confirmed',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'tuition_amount' => 'decimal:2',
        'food_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_confirmed' => 'boolean',
        'tuition_months' => 'json',
        'food_months' => 'json',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'discount_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function images()
    {
        return $this->hasMany(PaymentImage::class, 'payment_id');
    }

    // ດຶງຄ່າເດືອນຄ່າຮຽນໃນຮູບແບບອາເຣ
    public function getTuitionMonthsArrayAttribute()
    {
        if (empty($this->tuition_months)) {
            return [];
        }
        return json_decode($this->tuition_months, true);
    }

    // ດຶງຄ່າເດືອນຄ່າອາຫານໃນຮູບແບບອາເຣ
    public function getFoodMonthsArrayAttribute()
    {
        if (empty($this->food_months)) {
            return [];
        }
        return json_decode($this->food_months, true);
    }

    // ສ້າງລາຍການເດືອນສຳລັບ dropdown
    public static function getMonthOptions()
    {
        return [
            '1' => 'ມັງກອນ (1)',
            '2' => 'ກຸມພາ (2)',
            '3' => 'ມີນາ (3)',
            '4' => 'ເມສາ (4)',
            '5' => 'ພຶດສະພາ (5)',
            '6' => 'ມິຖຸນາ (6)',
            '7' => 'ກໍລະກົດ (7)',
            '8' => 'ສິງຫາ (8)',
            '9' => 'ກັນຍາ (9)',
            '10' => 'ຕຸລາ (10)',
            '11' => 'ພະຈິກ (11)',
            '12' => 'ທັນວາ (12)',
        ];
    }
}
