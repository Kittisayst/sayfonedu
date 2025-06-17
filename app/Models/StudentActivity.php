<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentActivity extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_activities' ໂດຍ default.
     */
    // protected $table = 'student_activities';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'student_activity_id';

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
        'activity_id', // Foreign key
        'student_id', // Foreign key
        'join_date',
        'status',
        'performance',
        'notes',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'join_date' => 'date:Y-m-d', // ແປງເປັນ Date object
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ການເຂົ້າຮ່ວມນີ້ກ່ຽວຂ້ອງກັບ Activity ດຽວ.
     */
    public function activity(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ ExtracurricularActivity
        return $this->belongsTo(ExtracurricularActivity::class, 'activity_id', 'activity_id');
    }

    /**
     * Relationship: ການເຂົ້າຮ່ວມນີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ Student
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Note: The Many-to-Many relationships (Student <-> Activity)
    // are defined in the Student and ExtracurricularActivity models,
    // referencing this pivot table.
}