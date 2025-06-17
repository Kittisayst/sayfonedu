<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class AcademicYear extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'academic_years' ໂດຍ default.
     */
    // protected $table = 'academic_years';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'academic_year_id';

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
        'year_name',
        'start_date',
        'end_date',
        'is_current',
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
        'is_current' => 'boolean',     // ແປງເປັນ Boolean
    ];

    // ========================================================================
    // Relationships: Has Many (One-to-Many)
    // ========================================================================

    /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ Classes.
     */
    public function classes(): HasMany
    {
        // ສົມມຸດ Model ຊື່ SchoolClass (ປ່ຽນຈາກ Classes ເພື່ອບໍ່ໃຫ້ຊ້ຳ Keyword)
        return $this->hasMany(SchoolClass::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ StudentEnrollments.
     */
    public function studentEnrollments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentEnrollment
        return $this->hasMany(StudentEnrollment::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ Examinations.
     */
    public function examinations(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Examination
        return $this->hasMany(Examination::class, 'academic_year_id', 'academic_year_id');
    }

     /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ Schedules.
     */
    public function schedules(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Schedule
        return $this->hasMany(Schedule::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ ExtracurricularActivities.
     */
    public function extracurricularActivities(): HasMany
    {
         // ສົມມຸດ Model ຊື່ ExtracurricularActivity
        return $this->hasMany(ExtracurricularActivity::class, 'academic_year_id', 'academic_year_id');
    }

    /**
     * Relationship: AcademicYear ໜຶ່ງສົກຮຽນ ມີຫຼາຍ StudentAttendanceSummaries.
     */
    public function studentAttendanceSummaries(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentAttendanceSummary
        return $this->hasMany(StudentAttendanceSummary::class, 'academic_year_id', 'academic_year_id');
    }

    // Note: Application logic should ensure end_date > start_date.
    // Note: Logic should ensure only one academic year has is_current = TRUE at any time.
}