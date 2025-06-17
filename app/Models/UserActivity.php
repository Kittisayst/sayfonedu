<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class UserActivity extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'user_activities' ໂດຍ default.
     */
    // protected $table = 'user_activities';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     * ເນື່ອງຈາກຕາຕະລາງ Log ນີ້ມີ created_at (ແລະ activity_time) ແຕ່ບໍ່ມີ updated_at, ເຮົາຈະປິດ timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * ກຳນົດຊື່ຟີລດ໌ created_at ໃຫ້ Laravel ຮູ້ຈັກ ເມື່ອ $timestamps = false.
     * ອາດຈະບໍ່ຈຳເປັນ ຖ້າຊື່ຟີລດ໌ກົງກັບ default ('created_at').
     */
     const CREATED_AT = 'created_at';
     // const UPDATED_AT = null; // Explicitly disable updated_at if needed

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * ໂດຍທົ່ວໄປ Log ອາດຈະບໍ່ mass assign, ແຕ່ກຳນົດໄວ້ກໍ່ໄດ້.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Foreign key
        'activity_type',
        'description',
        'ip_address',
        'user_agent',
        'activity_time',
        // created_at ໂດຍທົ່ວໄປບໍ່ຄວນ mass assign
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'activity_time' => 'datetime', // Cast ເປັນ datetime object (Carbon)
        'created_at' => 'datetime',    // Cast created_at ທີ່ກຳນົດເອງ
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ກິດຈະກຳນີ້ຖືກເຮັດໂດຍ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}