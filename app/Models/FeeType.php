<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class FeeType extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'fee_types' ໂດຍ default.
     */
    // protected $table = 'fee_types';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'fee_type_id';

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
        'level_id',
        'fee_name',
        'fee_description',
        'amount',
        'is_recurring',
        'recurring_interval',
        'is_mandatory',
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',      // Cast amount ເປັນ decimal 2 ຈຸດ
        'is_recurring' => 'boolean',  // Cast ເປັນ boolean
        'is_mandatory' => 'boolean',  // Cast ເປັນ boolean
        'is_active' => 'boolean',     // Cast ເປັນ boolean
    ];

   public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class, 'level_id')->orderBy('level_id');
    }

    // Note: Application logic should validate amount >= 0 and
    // consistency between is_recurring and recurring_interval.
}