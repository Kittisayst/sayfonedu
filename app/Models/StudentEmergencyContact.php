<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentEmergencyContact extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_emergency_contacts' ໂດຍ default.
     */
    // protected $table = 'student_emergency_contacts';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'contact_id';

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
        'contact_name',
        'relationship',
        'phone',
        'alternative_phone',
        'address',
        'priority',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority' => 'integer', // Cast priority ເປັນ integer
    ];

    /**
     * Relationship: ຂໍ້ມູນຜູ້ຕິດຕໍ່ສຸກເສີນນີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}