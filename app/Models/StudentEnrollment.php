<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentEnrollment extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_enrollments' ໂດຍ default.
     */
    // protected $table = 'student_enrollments';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'enrollment_id';

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
        'academic_year_id', // Foreign key
        'enrollment_date',
        'enrollment_status',
        'previous_class_id', // Foreign key
        'is_new_student',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrollment_date' => 'date:Y-m-d', // ແປງເປັນ Date object
        'is_new_student' => 'boolean',     // ແປງເປັນ Boolean
    ];

    /**
     * Relationship: ການລົງທະບຽນນີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: ການລົງທະບຽນນີ້ຢູ່ໃນ Class ຫ້ອງດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass (ສຳລັບຕາຕະລາງ classes)
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: ການລົງທະບຽນນີ້ຢູ່ໃນ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id')->where('status', 'active');
    }

    /**
     * Relationship: ການລົງທະບຽນນີ້ອາດຈະມີການອ້າງອີງເຖິງຫ້ອງຮຽນກ່ອນໜ້າ (Previous Class).
     */
    public function previousClass(): BelongsTo
    {
         // ສົມມຸດ Model ຊື່ SchoolClass (ສຳລັບຕາຕະລາງ classes)
        return $this->belongsTo(SchoolClass::class, 'previous_class_id', 'class_id');
    }

    // Note: UQ constraint (student_id, academic_year_id) in migration assumes one main enrollment record per year.
}