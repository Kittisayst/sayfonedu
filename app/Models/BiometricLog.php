<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class BiometricLog extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'biometric_logs' ໂດຍ default.
     */
    // protected $table = 'biometric_logs';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     * ເນື່ອງຈາກຕາຕະລາງ Log ນີ້ມີ created_at (ແລະ log_time) ແຕ່ບໍ່ມີ updated_at, ເຮົາຈະປິດ timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * ກຳນົດຊື່ຟີລດ໌ created_at ໃຫ້ Laravel ຮູ້ຈັກ ເມື່ອ $timestamps = false.
     * ອາດຈະບໍ່ຈຳເປັນ ຖ້າຊື່ຟີລດ໌ກົງກັບ default ('created_at').
     */
     const CREATED_AT = 'created_at';
     // const UPDATED_AT = null; // ບອກໃຫ້ຊັດເຈນວ່າບໍ່ມີ updated_at

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * ໂດຍທົ່ວໄປ Log ອາດຈະບໍ່ mass assign, ແຕ່ກຳນົດໄວ້ກໍ່ໄດ້.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Foreign key
        'biometric_id', // Foreign key
        'log_type',
        'status',
        'device_id',
        'location',
        'log_time',
        // created_at ໂດຍທົ່ວໄປບໍ່ຄວນ mass assign
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'log_time' => 'datetime', // Cast ເປັນ datetime object (Carbon)
        'created_at' => 'datetime', // Cast created_at ທີ່ກຳນົດເອງ
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: Log ນີ້ກ່ຽວຂ້ອງກັບ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: Log ນີ້ອາດຈະກ່ຽວຂ້ອງກັບ BiometricData record ອັນດຽວ (ຖ້າມີ).
     */
    public function biometricData(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ BiometricData
        return $this->belongsTo(BiometricData::class, 'biometric_id', 'biometric_id');
    }
}