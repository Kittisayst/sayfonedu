<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Backup extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'backups' ໂດຍ default.
     */
    // protected $table = 'backups';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'backup_id';

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
        'backup_name',
        'backup_type',
        'file_path',
        'file_size',
        'backup_date',
        'status',
        'initiated_by', // Foreign key
        'description',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'backup_date' => 'datetime', // Cast ເປັນ datetime object (Carbon)
        'file_size' => 'integer', // Cast bigInteger ເປັນ integer (PHP ຈັດການໄດ້)
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ການ Backup ນີ້ຖືກລິເລີ່ມໂດຍ User ຄົນດຽວ (ຖ້າມີ).
     */
    public function initiator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ລິເລີ່ມ
        return $this->belongsTo(User::class, 'initiated_by', 'user_id');
    }

    // Note: Application logic should validate file_size >= 0 if necessary.
}