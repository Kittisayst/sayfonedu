<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Grade extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'grades' ໂດຍ default.
     */
    // protected $table = 'grades';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'grade_id';

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
        'class_id', // Foreign key
        'subject_id', // Foreign key
        'exam_id', // Foreign key
        'marks',
        'grade_letter',
        'comments',
        'is_published',
        'graded_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'marks' => 'decimal:2', // Cast marks ເປັນ decimal 2 ຈຸດ
        'is_published' => 'boolean', // Cast is_published ເປັນ boolean
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: Grade ນີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: Grade ນີ້ຢູ່ໃນ Class ຫ້ອງດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Grade ນີ້ເປັນຂອງ Subject ວິຊາດຽວ.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: Grade ນີ້ເປັນຂອງ Examination ອັນດຽວ.
     */
    public function examination(): BelongsTo
    {
        return $this->belongsTo(Examination::class, 'exam_id', 'exam_id');
    }

    /**
     * Relationship: Grade ນີ້ຖືກໃຫ້ຄະແນນໂດຍ User ຄົນດຽວ (ຜູ້ໃຫ້ຄະແນນ).
     */
    public function grader(): BelongsTo
    {
        // ອາດຈະໝາຍເຖິງ Teacher ຫຼື User ອື່ນ
        return $this->belongsTo(User::class, 'graded_by', 'user_id');
    }

    // Note: Application logic should validate marks range (>= 0 and <= Examinations.total_marks).
}