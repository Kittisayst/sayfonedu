<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class Province extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'provinces'
     */
    // protected $table = 'provinces';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'province_id';

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
        'province_name_lao',
        'province_name_en',
    ];

    /**
     * Relationship: Province ໜຶ່ງແຂວງມີຫຼາຍເມືອງ (Districts).
     */
    public function districts(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ District
        return $this->hasMany(District::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: Province ໜຶ່ງແຂວງມີນັກຮຽນຫຼາຍຄົນ (Students) ອາໄສຢູ່.
     */
    public function students(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Student
        return $this->hasMany(Student::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: Province ໜຶ່ງແຂວງມີຜູ້ປົກຄອງຫຼາຍຄົນ (Guardian) ອາໄສຢູ່.
     */
    public function parents(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Parents
        return $this->hasMany(StudentParent::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: Province ໜຶ່ງແຂວງມີຄູສອນຫຼາຍຄົນ (Teachers) ອາໄສຢູ່.
     */
    public function teachers(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Teacher
        return $this->hasMany(Teacher::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: Province ໜຶ່ງແຂວງອາດຈະມີການບັນທຶກໃນ StudentPreviousLocations ຫຼາຍຄັ້ງ.
     */
    public function studentPreviousLocations(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ StudentPreviousLocation
         return $this->hasMany(StudentPreviousLocation::class, 'province_id', 'province_id');
    }
}