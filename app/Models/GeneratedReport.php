<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class GeneratedReport extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'generated_reports' ໂດຍ default.
     */
    // protected $table = 'generated_reports';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'report_id';

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
        'report_name',
        'template_id', // Foreign key
        'report_type',
        'report_data',
        'report_format',
        'file_path',
        'generated_by', // Foreign key
        'generated_at', // Timestamp ສະເພາະຕອນ generate
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'generated_at' => 'datetime', // ແປງເປັນ datetime object
        // ຖ້າ report_data ເກັບເປັນ JSON, ສາມາດເພີ່ມ:
        // 'report_data' => 'array',
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ລາຍງານນີ້ຖືກສ້າງມາຈາກ Template ດຽວ.
     */
    public function template(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ ReportTemplate
        return $this->belongsTo(ReportTemplate::class, 'template_id', 'template_id');
    }

    /**
     * Relationship: ລາຍງານນີ້ຖືກສ້າງໂດຍ User ຄົນດຽວ.
     */
    public function generator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ສ້າງ
        return $this->belongsTo(User::class, 'generated_by', 'user_id');
    }
}