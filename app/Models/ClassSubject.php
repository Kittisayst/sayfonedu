<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class ClassSubject extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'class_subjects' ໂດຍ default.
     */
    protected $table = 'class_subjects'; // ກຳນົດໃຫ້ຊັດເຈນກໍ່ໄດ້

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'class_subject_id';

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
        'hours_per_week',
        'day_of_week',
        'start_time',
        'end_time',
        'status',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ອາດຈະ cast TIME columns ຖ້າຕ້ອງການຈັດການເປັນ object ສະເພາະ
        // 'start_time' => 'datetime:H:i:s',
        // 'end_time' => 'datetime:H:i:s',
    ];

    /**
     * Relationship: ການມອບໝາຍນີ້ຂຶ້ນກັບ Class ດຽວ.
     * ໃຊ້ຊື່ schoolClass ເພື່ອຫຼີກລ້ຽງການຊ້ຳກັບ keyword 'class'
     */
    public function schoolClass(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ SchoolClass (ສຳລັບຕາຕະລາງ classes)
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    /**
     * Relationship: ການມອບໝາຍນີ້ກ່ຽວຂ້ອງກັບ Subject ດຽວ.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relationship: ການມອບໝາຍນີ້ອາດຈະມີ Teacher ຄົນດຽວຮັບຜິດຊອບ.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }

    // ໝາຍເຫດ: Relationships ແບບ Many-to-Many ລະຫວ່າງ Class, Subject, Teacher
    // ຄວນຖືກກຳນົດໃນ Models ຫຼັກເຫຼົ່ານັ້ນ (ເຊັ່ນ: Subject->classes(), SchoolClass->subjects())
    // ໂດຍອ້າງອີງຜ່ານຕາຕະລາງ class_subjects ນີ້.
}