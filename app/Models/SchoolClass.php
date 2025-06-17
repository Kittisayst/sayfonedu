<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;       // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;       // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import BelongsToMany

// ໃຊ້ຊື່ SchoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ PHP Keyword 'Class'
class SchoolClass extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ຕ້ອງກຳນົດເອງ ເພາະຊື່ Model 'SchoolClass' ບໍ່ກົງກັບຊື່ຕາຕະລາງ 'classes'.
     *
     * @var string
     */
    protected $table = 'classes'; // ບອກໃຫ້ Model ນີ້ໃຊ້ຕາຕະລາງ 'classes'

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'class_id';

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
        'class_name',
        'grade_level',
        'academic_year_id', // Foreign key
        'homeroom_teacher_id', // Foreign key
        'room_number',
        'capacity',
        'description',
        'status',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer', // Cast ເປັນ integer (ຈະເປັນ null ຖ້າ DB ເປັນ NULL)
        // status ເປັນ ENUM ມັກຈະຈັດການເປັນ string ໂດຍ default
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: Class ນີ້ຂຶ້ນກັບ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: Class ນີ້ອາດຈະມີ Homeroom Teacher ຄົນດຽວ.
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id', 'teacher_id');
    }

    // ========================================================================
    // Relationships: Has Many / Belongs To Many
    // ========================================================================

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີຫຼາຍ StudentEnrollments.
     */
    public function studentEnrollments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentEnrollment
        return $this->hasMany(StudentEnrollment::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີຫຼາຍ Schedules.
     */
    public function schedules(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Schedule
        return $this->hasMany(Schedule::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີຫຼາຍ Grades.
     */
    public function grades(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Grade
        return $this->hasMany(Grade::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີຫຼາຍ Attendance records.
     */
    public function attendanceRecords(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Attendance
        return $this->hasMany(Attendance::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີການມອບໝາຍ ClassSubjects ຫຼາຍອັນ.
     */
    public function classSubjectAssignments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ ClassSubject
        return $this->hasMany(ClassSubject::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: Class ໜຶ່ງຫ້ອງ ມີການສອນຫຼາຍ Subjects (ຜ່ານ class_subjects).
     */
    public function subjects(): BelongsToMany
    {
        // ສົມມຸດ Model ຊື່ Subject
        // belongsToMany(RelatedModel, pivot_table, foreign_pivot_key, related_pivot_key)
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
                    ->withPivot('teacher_id', 'hours_per_week', 'day_of_week', 'start_time', 'end_time', 'status') // ເອົາຂໍ້ມູນຈາກ Pivot Table ມາພ້ອມ
                    ->withTimestamps(); // ຕາຕະລາງ Pivot ມີ timestamps
    }

    // Note: Application logic should validate capacity > 0 if necessary.
}