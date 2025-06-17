<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Request extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'requests' ໂດຍ default.
     */
    // protected $table = 'requests';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'request_id';

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
        'user_id', // Foreign key
        'request_type',
        'subject',
        'content',
        'status',
        'response',
        'attachment',
        'handled_by', // Foreign key
        'handled_at',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'handled_at' => 'datetime', // Cast ເປັນ datetime object (Carbon)
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ຄຳຮ້ອງນີ້ຖືກຍື່ນໂດຍ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ຍື່ນຄຳຮ້ອງ
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: ຄຳຮ້ອງນີ້ຖືກດຳເນີນການໂດຍ User ຄົນດຽວ (ຖ້າມີ).
     */
    public function handler(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ດຳເນີນການ
        return $this->belongsTo(User::class, 'handled_by', 'user_id');
    }
}