<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Discount extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'discounts' ໂດຍ default.
     */
    // protected $table = 'discounts';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'discount_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     *
     * @var bool
     */
    public $timestamps = true; // ເພາະໃນ Migration ເຮົາໃຊ້ $table->timestamps()

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discount_name',
        'discount_type',
        'discount_value',
        'description',
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'discount_value' => 'decimal:2', // Cast ເປັນ decimal 2 ຈຸດ
        'is_active' => 'boolean',     // Cast ເປັນ boolean
    ];

    // ຄວາມສຳພັນກັບຕາຕະລາງ Payment
    public function payments()
    {
        return $this->hasMany(Payment::class, 'discount_id');
    }

    /**
     * ສ້າງປ້າຍກຳກັບສຳລັບສະແດງຜົນທີ່ມີຮູບແບບ ຊື່ສ່ວນຫຼຸດ: ຄ່າສ່ວນຫຼຸດ
     * 
     * @return string
     */
    public function getFormattedLabel(): string
    {
        $valueDisplay = $this->discount_type === 'percentage'
            ? "{$this->discount_value}%"
            : number_format($this->discount_value, 0) . " ກີບ";

        return "{$this->discount_name}: {$valueDisplay}";
    }

    /**
     * ດຶງລາຍການສ່ວນຫຼຸດທັງໝົດທີ່ເປີດໃຊ້ງານສຳລັບໃຊ້ໃນ dropdown
     * 
     * @return array
     */
    public static function getSelectOptions(): array
    {
        return static::where('is_active', true)
            ->get()
            ->mapWithKeys(function ($discount) {
                return [$discount->discount_id => $discount->getFormattedLabel()];
            })
            ->toArray();
    }

    // Note: Application logic should validate discount_value range
    // (e.g., >= 0, and <= 100 if type is 'percentage').
}
