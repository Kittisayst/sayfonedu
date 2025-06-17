<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Religion extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'religions'
     */
    // protected $table = 'religions';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'religion_id';

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
        'religion_name_lao',
        'religion_name_en',
    ];

    /**
     * Relationship: Religion ໜຶ່ງສາສະໜາ ມີຫຼາຍນັກຮຽນ (Students).
     */
    public function students(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Student
        return $this->hasMany(Student::class, 'religion_id', 'religion_id');
    }
}