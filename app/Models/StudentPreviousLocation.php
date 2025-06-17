<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentPreviousLocation extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_previous_locations' ໂດຍ default.
     */
    // protected $table = 'student_previous_locations';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'location_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id', // Foreign key
        'address',
        'village_id', // Foreign key
        'district_id', // Foreign key
        'province_id', // Foreign key
        'country',
        'from_date',
        'to_date',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'from_date' => 'date:Y-m-d', // ແປງເປັນ Date object
        'to_date' => 'date:Y-m-d',   // ແປງເປັນ Date object
    ];

    /**
     * Relationship: ຂໍ້ມູນທີ່ຢູ່ເກົ່ານີ້ເປັນຂອງ Student ຄົນດຽວ.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: ຂໍ້ມູນທີ່ຢູ່ເກົ່ານີ້ອາດຈະຢູ່ໃນ Village ດຽວ.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    /**
     * Relationship: ຂໍ້ມູນທີ່ຢູ່ເກົ່ານີ້ອາດຈະຢູ່ໃນ District ດຽວ.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: ຂໍ້ມູນທີ່ຢູ່ເກົ່ານີ້ອາດຈະຢູ່ໃນ Province ດຽວ.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }
}