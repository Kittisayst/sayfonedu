<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class TeacherDocument extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'teacher_documents' ໂດຍ default.
     */
    // protected $table = 'teacher_documents';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'document_id';

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
        'teacher_id', // Foreign key
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'file_type',
        'upload_date',
        'description',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'upload_date' => 'datetime', // ແປງເປັນ datetime object
        // 'file_size' ເປັນ integer ຢູ່ແລ້ວ
    ];

    /**
     * Relationship: ເອກະສານນີ້ເປັນຂອງ Teacher ຄົນດຽວ.
     */
    public function teacher(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    // Note: Consider adding a unique constraint in migration if needed,
    // e.g., unique(['teacher_id', 'document_name']) or unique(['teacher_id', 'document_type'])
    // depending on requirements.
}