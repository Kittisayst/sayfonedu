<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Expense extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'expenses' ໂດຍ default.
     */
    // protected $table = 'expenses';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'expense_id';

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
        'expense_category',
        'amount',
        'expense_date',
        'description',
        'payment_method',
        'receipt_number',
        'receipt_image',
        'approved_by', // Foreign key
        'created_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',      // Cast amount ເປັນ decimal 2 ຈຸດ
        'expense_date' => 'date:Y-m-d', // Cast ເປັນ Date object
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ລາຍຈ່າຍນີ້ຖືກອະນຸມັດໂດຍ User ຄົນດຽວ (ຖ້າມີ).
     */
    public function approver(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ອະນຸມັດ
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    /**
     * Relationship: ລາຍຈ່າຍນີ້ຖືກສ້າງ/ບັນທຶກໂດຍ User ຄົນດຽວ.
     */
    public function creator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ສ້າງ
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    // Note: Application logic should validate amount > 0 if necessary.
}