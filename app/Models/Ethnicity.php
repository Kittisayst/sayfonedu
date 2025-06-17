<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Ethnicity extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'ethnicities'
     */
    // protected $table = 'ethnicities';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'ethnicity_id';

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
        'ethnicity_name_lao',
        'ethnicity_name_en',
    ];

    /**
     * Relationship: Ethnicity ໜຶ່ງຊົນເຜົ່າ ມີຫຼາຍນັກຮຽນ (Students).
     */
    public function students(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Student
        return $this->hasMany(Student::class, 'ethnicity_id', 'ethnicity_id');
    }
}