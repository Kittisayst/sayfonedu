<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class BiometricData extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'biometric_data' ໂດຍ default.
     */
    protected $table = 'biometric_data'; // กำหนดให้ชัดเจนก็ได้

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'biometric_id';

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
        'biometric_type',
        'biometric_data',
        'status',
    ];

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ຄວນເຊື່ອງໄວ້ ເວລາປ່ຽນເປັນ array ຫຼື JSON.
     * ເຮົາຄວນເຊື່ອງ biometric_data ເພາະເປັນຂໍ້ມູນ sensitive ແລະ ອາດຈະໃຫຍ່.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'biometric_data',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        // biometric_data (LONGBLOB) ໂດຍປົກກະຕິຈະຖືກດຶງມາເປັນ string ຫຼື stream,
        // ບໍ່ຈຳເປັນຕ້ອງ cast ໃນ Model ເວັ້ນແຕ່ມີການຈັດການພິເສດ.
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * Relationship: ຂໍ້ມູນ Biometric ນີ້ເປັນຂອງ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: ຂໍ້ມູນ Biometric ນີ້ມີຫຼາຍ BiometricLogs.
     * (ໝາຍເຖິງ Log ການໃຊ້ຂໍ້ມູນຊີວະມິຕິ record ນີ້ໂດຍສະເພາະ)
     */
    public function logs(): HasMany
    {
        // ສົມມຸດ Model ຊື່ BiometricLog
        return $this->hasMany(BiometricLog::class, 'biometric_id', 'biometric_id');
    }

    // Note: Consider adding a unique constraint in migration based on policy
    // (e.g., unique per user per type, or per user per finger index).
}