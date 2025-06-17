<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class SchoolStoreItem extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'school_store_items' ໂດຍ default.
     */
    // protected $table = 'school_store_items';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'item_id';

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
        'item_name',
        'item_code',
        'category',
        'description',
        'unit_price',
        'stock_quantity',
        'reorder_level',
        'item_image',
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',  // Cast ເປັນ decimal 2 ຈຸດ
        'stock_quantity' => 'integer', // Cast ເປັນ integer
        'reorder_level' => 'integer',  // Cast ເປັນ integer (ຈະເປັນ null ຖ້າ DB ເປັນ NULL)
        'is_active' => 'boolean',     // Cast ເປັນ boolean
    ];

    // ========================================================================
    // Relationships: Has Many
    // ========================================================================

    /**
     * Relationship: ສິນຄ້າໜຶ່ງລາຍການ ມີຫຼາຍ StoreSales.
     */
    public function sales(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StoreSale
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
        return $this->hasMany(StoreSale::class, 'item_id', 'item_id');
    }

    // Note: Application logic should validate unit_price >= 0,
    // stock_quantity >= 0, and reorder_level (if not null) >= 0.
}