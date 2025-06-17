<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Schedule extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'schedules' ໂດຍ default.
     */
    // protected $table = 'schedules';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'schedule_id';

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
        'class_id', // Foreign key
        'subject_id', // Foreign key
        'teacher_id', // Foreign key
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'academic_year_id', // Foreign key
        'is_active',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Cast TIME ເປັນ datetime object ໂດຍກຳນົດ format ທີ່ບໍ່ມີວັນທີ
        // ເພື່ອໃຫ້ໃຊ້ Carbon function ກັບເວລາໄດ້.
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'is_active' => 'boolean',     // Cast ເປັນ boolean
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ຕາຕະລາງນີ້ເປັນຂອງ Class ຫ້ອງດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: ຕາຕະລາງນີ້ເປັນຂອງ Subject ວິຊາດຽວ.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: ຕາຕະລາງນີ້ອາດຈະມີ Teacher ຄົນດຽວສອນ.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Relationship: ຕາຕະລາງນີ້ຢູ່ໃນ AcademicYear ດຽວ.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
    }

    // Note: Clash detection (class, room, teacher) is primarily handled by unique constraints in the migration.
    // Note: Application logic should validate end_time > start_time.
}