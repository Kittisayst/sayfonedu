<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StoreSale extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'store_sales' ໂດຍ default.
     */
    // protected $table = 'store_sales';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'sale_id';

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
        'item_id', // Foreign key
        'quantity',
        'unit_price',
        'total_price', // Calculated field
        'discount',
        'final_price', // Calculated field
        'buyer_type',
        'buyer_id', // Polymorphic Foreign key
        'sale_date',
        'payment_method',
        'sold_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'sale_date' => 'datetime', // Cast timestamp ເປັນ datetime object
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ການຂາຍນີ້ກ່ຽວຂ້ອງກັບ Item ລາຍການດຽວ.
     */
    public function item(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolStoreItem
        return $this->belongsTo(SchoolStoreItem::class, 'item_id', 'item_id');
    }

    /**
     * Relationship: ການຂາຍນີ້ຖືກບັນທຶກໂດຍ User ຄົນດຽວ (ຜູ້ຂາຍ).
     */
    public function seller(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ຂາຍ
        return $this->belongsTo(User::class, 'sold_by', 'user_id');
    }

    /**
     * Relationship: ເອົາຂໍ້ມູນຜູ້ຊື້ (ຈັດການ Polymorphic Relationship ເອງ).
     *
     * @return Model|null
     */
    public function buyer() // ບໍ່ສາມາດໃຊ້ morphTo ໂດຍກົງໄດ້ ເພາະ buyer_type ເປັນ ENUM
    {
        if ($this->buyer_type && $this->buyer_id) {
            $modelMap = [
                'student' => Student::class,
                'teacher' => Teacher::class,
                'parent'  => Guardian::class, // ໃຊ້ Guardian Model ທີ່ສ້າງໄວ້
                // 'other' ຈະ return null ຫຼື ຈັດການຕ່າງຫາກ
            ];

            if (isset($modelMap[$this->buyer_type])) {
                $buyerModelClass = $modelMap[$this->buyer_type];
                // ສົມມຸດວ່າ Primary Key ຂອງແຕ່ລະ Model ແມ່ນ 'student_id', 'teacher_id', 'parent_id'
                // ຖ້າບໍ່ແມ່ນ, ຕ້ອງປັບປ່ຽນ $this->getKeyName() ຫຼື ລະບຸຊື່ key ເອງ
                // ໃຊ້ find() ເພື່ອໃຫ້ໄດ້ null ຖ້າບໍ່ພົບ record
                return $buyerModelClass::find($this->buyer_id);
            }
        }
        return null;
    }

    // Note: total_price and final_price should be calculated automatically.
    // Note: Application logic needed to link buyer_id to the correct table based on buyer_type.
    // Note: Application logic should validate quantity > 0, prices >= 0, discount <= total_price.
    // Note: When a sale is recorded, application logic/trigger should decrease stock_quantity in school_store_items (D52).
}