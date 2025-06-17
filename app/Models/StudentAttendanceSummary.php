<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentAttendanceSummary extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ຕ້ອງກຳນົດເອງ ເພາະຊື່ຕາຕະລາງໃນ Migration ('student_attendance_summary')
     * ບໍ່ກົງກັບ default ຂອງ Laravel ('student_attendance_summaries').
     *
     * @var string
     */
    protected $table = 'student_attendance_summary';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'summary_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     *
     * @var bool
     */
    public $timestamps = true; // ເພາະໃນ Migration ເຮົາໃຊ້ $table->timestamps()

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * ໂດຍທົ່ວໄປຕາຕະລາງ Summary ມັກຈະບໍ່ຖືກ mass assign ໂດຍກົງ
     * ແຕ່ຈະຖືກສ້າງ/ອັບເດດຜ່ານ Process ສະຫຼຸບຂໍ້ມູນ.
     * ແຕ່ກຳນົດໄວ້ກໍ່ບໍ່ເສຍຫາຍຫຍັງ.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id', // Foreign key
        'class_id', // Foreign key
        'academic_year_id', // Foreign key
        'month',
        'year',
        'total_days',
        'present_days',
        'absent_days',
        'late_days',
        'excused_days',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_days' => 'integer',
        'present_days' => 'integer',
        'absent_days' => 'integer',
        'late_days' => 'integer',
        'excused_days' => 'integer',
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ຂໍ້ມູນສະຫຼຸບນີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: ຂໍ້ມູນສະຫຼຸບນີ້ກ່ຽວຂ້ອງກັບ Class ຫ້ອງດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: ຂໍ້ມູນສະຫຼຸບນີ້ຢູ່ໃນ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    // Note: This table is typically populated by a process summarizing data
    // from the 'attendance' (D34) table for reporting efficiency.
    // Note: Application logic/process should validate day counts if necessary.
}