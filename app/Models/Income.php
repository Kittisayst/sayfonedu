<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Income extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'income' ໂດຍ default (singular form).
     */
    // protected $table = 'income';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'income_id';

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
        'income_category',
        'amount',
        'income_date',
        'description',
        'payment_method',
        'receipt_number',
        'received_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',      // Cast amount ເປັນ decimal 2 ຈຸດ
        'income_date' => 'date:Y-m-d', // Cast ເປັນ Date object
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ລາຍຮັບນີ້ຖືກຮັບ/ບັນທຶກໂດຍ User ຄົນດຽວ.
     */
    public function receiver(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ຮັບ/ບັນທຶກ
        return $this->belongsTo(User::class, 'received_by', 'user_id');
    }

    // Note: Application logic should validate amount > 0 if necessary.
}