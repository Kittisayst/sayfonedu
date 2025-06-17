<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;           // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany;    // Import BelongsToMany

class Subject extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'subjects'
     */
    // protected $table = 'subjects';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'subject_id';

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
        'subject_code',
        'subject_name_lao',
        'subject_name_en',
        'credit_hours',
        'description',
        'category',
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // ແປງເປັນ Boolean
        'credit_hours' => 'integer', // ແປງເປັນ Integer (optional)
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * Relationship: Subject ໜຶ່ງວິຊາ ມີການມອບໝາຍໃຫ້ຫຼາຍ ClassSubjects.
     * (Relationship ຫາຕາຕະລາງເຊື່ອມໂຍງໂດຍກົງ)
     */
    public function classAssignments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ ClassSubject
        return $this->hasMany(ClassSubject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: Subject ໜຶ່ງວິຊາ ສອນໃນຫຼາຍ Classes (ຜ່ານ class_subjects).
     */
    public function classes(): BelongsToMany
    {
        // ສົມມຸດ Model ຊື່ SchoolClass (ປ່ຽນຈາກ Classes)
        // belongsToMany(RelatedModel, pivot_table, foreign_pivot_key, related_pivot_key)
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
                    ->withPivot('teacher_id', 'hours_per_week', 'day_of_week', 'start_time', 'end_time', 'status') // ເອົາຂໍ້ມູນຈາກ Pivot Table ມາພ້ອມ
                    ->withTimestamps(); // ຕາຕະລາງ Pivot ມີ timestamps
    }

    /**
     * Relationship: Subject ໜຶ່ງວິຊາ ມີຫຼາຍ Grades.
     */
    public function grades(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Grade
        return $this->hasMany(Grade::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: Subject ໜຶ່ງວິຊາ ມີຫຼາຍ Schedules.
     */
    public function schedules(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Schedule
        return $this->hasMany(Schedule::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: Subject ໜຶ່ງວິຊາ ມີຫຼາຍ Attendance records.
     */
    public function attendanceRecords(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Attendance
        return $this->hasMany(Attendance::class, 'subject_id', 'subject_id');
    }

    // Note: Application logic should validate credit_hours >= 0 if necessary.
}