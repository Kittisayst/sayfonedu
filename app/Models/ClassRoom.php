<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    /**
     * ຕາຕະລາງທີ່ກ່ຽວຂ້ອງກັບ model.
     *
     * @var string
     */
    protected $table = 'classes';

    /**
     * Primary key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'class_id';

    /**
     * ຄຸນສົມບັດທີ່ສາມາດກຳນົດຄ່າໄດ້.
     *
     * @var array
     */
    protected $fillable = [
        'class_name',
        'level_id',
        'academic_year_id',
        'homeroom_teacher_id',
        'room_number',
        'capacity',
        'description',
        'status',
    ];

    /**
     * ຄຸນສົມບັດທີ່ຄວນຖືກແປງເປັນປະເພດຂໍ້ມູນທີ່ເໝາະສົມ.
     *
     * @var array
     */
    protected $casts = [
        'capacity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * ຄ່າເລີ່ມຕົ້ນສຳລັບຄຸນສົມບັດບາງຢ່າງ.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'active',
    ];

    public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class, 'level_id', 'level_id')->orderBy('level_id', 'asc');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Academic Years.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id')->where('status', 'active');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Teachers (ຄູປະຈຳຫ້ອງ).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id', 'teacher_id');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Student Enrollments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'class_id', 'class_id');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Students (ຜ່ານ enrollments).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentEnrollment::class,
            'class_id', // Foreign key on StudentEnrollment table
            'student_id', // Foreign key on Student table
            'class_id', // Local key on ClassRoom table
            'student_id' // Local key on StudentEnrollment table
        );
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Class Subjects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id', 'class_id');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Subjects (ຜ່ານ ClassSubject).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subjects',
            'class_id',
            'subject_id',
            'class_id',
            'subject_id'
        );
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Schedules.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id', 'class_id');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Attendance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id', 'class_id');
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Grades.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'class_id', 'class_id');
    }

    /**
     * Scope ສຳລັບຄົ້ນຫາສະເພາະຫ້ອງຮຽນທີ່ມີສະຖານະເປັນ active.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope ສຳລັບຄົ້ນຫາຫ້ອງຮຽນຕາມສົກຮຽນປະຈຸບັນ.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentAcademicYear($query)
    {
        $currentAcademicYearId = AcademicYear::where('is_current', true)->value('academic_year_id');

        return $query->where('academic_year_id', $currentAcademicYearId);
    }
}