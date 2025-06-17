<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;     // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;       // Import HasMany
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import BelongsToMany

class ExtracurricularActivity extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'extracurricular_activities' ໂດຍ default.
     */
    // protected $table = 'extracurricular_activities';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'activity_id';

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
        'activity_name',
        'activity_type',
        'description',
        'start_date',
        'end_date',
        'schedule',
        'location',
        'max_participants',
        'coordinator_id', // Foreign key
        'academic_year_id', // Foreign key
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d',       // Cast ເປັນ Date object
        'end_date' => 'date:Y-m-d',         // Cast ເປັນ Date object
        'max_participants' => 'integer',    // Cast ເປັນ integer
        'is_active' => 'boolean',         // Cast ເປັນ boolean
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ກິດຈະກຳນີ້ມີຜູ້ປະສານງານ (User) ຄົນດຽວ (ຖ້າມີ).
     */
    public function coordinator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'coordinator_id', 'user_id');
    }

    /**
     * Relationship: ກິດຈະກຳນີ້ຢູ່ໃນ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        // ອ້າງອີງຫາ AcademicYear Model
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id')->orderBy('academic_year_id', 'desc');
    }

    // ========================================================================
    // Relationships: Has Many / Belongs To Many
    // ========================================================================

    /**
     * Relationship: ກິດຈະກຳນີ້ມີຫຼາຍ StudentActivities records (ຂໍ້ມູນການເຂົ້າຮ່ວມ).
     * (Relationship ຫາຕາຕະລາງເຊື່ອມໂຍງໂດຍກົງ)
     */
    public function studentActivities(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentActivity
        return $this->hasMany(StudentActivity::class, 'activity_id', 'activity_id');
    }

    /**
     * Relationship: ກິດຈະກຳນີ້ມີຫຼາຍ Students ເຂົ້າຮ່ວມ (ຜ່ານ student_activities).
     */
    public function students(): BelongsToMany
    {
        // ສົມມຸດ Model ຊື່ Student
        // belongsToMany(RelatedModel, pivot_table, foreign_pivot_key, related_pivot_key)
        return $this->belongsToMany(Student::class, 'student_activities', 'activity_id', 'student_id')
            ->withPivot('join_date', 'status', 'performance', 'notes') // ເອົາຂໍ້ມູນຈາກ Pivot Table ມາພ້ອມ
            ->withTimestamps(); // ຕາຕະລາງ Pivot ມີ timestamps
    }

    // Note: Application logic should validate date sequence and max_participants >= 0 if necessary.
}