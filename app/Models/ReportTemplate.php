<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class ReportTemplate extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'report_templates' ໂດຍ default.
     */
    // protected $table = 'report_templates';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'template_id';

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
        'template_name',
        'template_type',
        'template_content',
        'is_active',
        'created_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // Cast ເປັນ boolean
        // template_content ເປັນ TEXT/LONGTEXT ບໍ່ຈຳເປັນຕ້ອງ cast ໂດຍ default
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * Relationship: ແມ່ແບບນີ້ຖືກສ້າງໂດຍ User ຄົນດຽວ.
     */
    public function creator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ສ້າງ
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Relationship: ແມ່ແບບນີ້ຖືກໃຊ້ສ້າງ GeneratedReports ຫຼາຍອັນ.
     */
    public function generatedReports(): HasMany
    {
        // ສົມມຸດ Model ຊື່ GeneratedReport
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
        return $this->hasMany(GeneratedReport::class, 'template_id', 'template_id');
    }
}