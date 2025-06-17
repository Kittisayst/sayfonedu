<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class Examination extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'examinations' ໂດຍ default.
     */
    // protected $table = 'examinations';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'exam_id';

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
        'exam_name',
        'exam_type',
        'academic_year_id', // Foreign key
        'start_date',
        'end_date',
        'description',
        'total_marks',
        'passing_marks',
        'status',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d', // ແປງເປັນ Date object
        'end_date' => 'date:Y-m-d',   // ແປງເປັນ Date object
        'total_marks' => 'integer',   // ແປງເປັນ Integer
        'passing_marks' => 'integer', // ແປງເປັນ Integer (ຈະເປັນ null ຖ້າ DB ເປັນ NULL)
    ];

    /**
     * Relationship: ການສອບເສັງນີ້ຂຶ້ນກັບ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: ການສອບເສັງນີ້ມີຫຼາຍ Grades.
     */
    public function grades(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Grade
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
        return $this->hasMany(Grade::class, 'exam_id', 'exam_id');
    }

    // Note: Application logic should validate date sequence, total_marks > 0,
    // and passing_marks relation to total_marks if necessary.
}