<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentPreviousEducation extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_previous_education' ໂດຍ default (snake_case ຂອງຊື່ class).
     */
    // protected $table = 'student_previous_education';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'education_id';

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
        'student_id', // Foreign key
        'school_name',
        'education_level',
        'from_year',
        'to_year',
        'certificate',
        'gpa',
        'description',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Cast GPA ເປັນ decimal ຕາມທີ່ກຳນົດໃນ migration (2 ຈຸດ)
        'gpa' => 'decimal:2',
        // from_year and to_year ເປັນ integer ຢູ່ແລ້ວ
    ];

    /**
     * Relationship: ປະຫວັດການສຶກສານີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Note: Application logic should validate year sequence (to_year >= from_year)
    // and GPA range if necessary.
}