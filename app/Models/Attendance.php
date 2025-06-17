<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Attendance extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ຕ້ອງກຳນົດເອງ ເພາະຊື່ຕາຕະລາງໃນ Migration ('attendance')
     * ບໍ່ກົງກັບ default ຂອງ Laravel ('attendances').
     *
     * @var string
     */
    protected $table = 'attendance';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'attendance_id';

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
        'attendance_date',
        'status',
        'reason',
        'recorded_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attendance_date' => 'date:Y-m-d', // Cast ເປັນ Date object
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ບັນທຶກການຂາດ-ມານີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: ບັນທຶກການຂາດ-ມານີ້ກ່ຽວຂ້ອງກັບ Class ຫ້ອງດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: ບັນທຶກການຂາດ-ມານີ້ອາດຈະກ່ຽວຂ້ອງກັບ Subject ວິຊາດຽວ.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: ບັນທຶກການຂາດ-ມານີ້ຖືກບັນທຶກໂດຍ User ຄົນດຽວ (ຖ້າມີ).
     */
    public function recorder(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ບັນທຶກ
        return $this->belongsTo(User::class, 'recorded_by', 'user_id');
    }
}