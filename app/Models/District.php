<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class District extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'districts'
     */
    // protected $table = 'districts';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'district_id';

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
        'district_name_lao',
        'district_name_en',
        'province_id', // Foreign key ຕ້ອງຢູ່ໃນ fillable ຖ້າຕ້ອງການກຳນົດຕອນສ້າງ District ຜ່ານ mass assignment
    ];

    /**
     * Relationship: District ໜຶ່ງເມືອງຂຶ້ນກັບ Province ດຽວ.
     */
    public function province(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: District ໜຶ່ງເມືອງມີຫຼາຍບ້ານ (Villages).
     */
    public function villages(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Village
        return $this->hasMany(Village::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: District ໜຶ່ງເມືອງມີນັກຮຽນຫຼາຍຄົນ (Students) ອາໄສຢູ່.
     */
    public function students(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Student
        return $this->hasMany(Student::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: District ໜຶ່ງເມືອງມີຜູ້ປົກຄອງຫຼາຍຄົນ (Parents) ອາໄສຢູ່.
     */
    public function parents(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Parents
        return $this->hasMany(StudentParent::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: District ໜຶ່ງເມືອງມີຄູສອນຫຼາຍຄົນ (Teachers) ອາໄສຢູ່.
     */
    public function teachers(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Teacher
        return $this->hasMany(Teacher::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: District ໜຶ່ງເມືອງອາດຈະມີການບັນທຶກໃນ StudentPreviousLocations ຫຼາຍຄັ້ງ.
     */
    public function studentPreviousLocations(): HasMany
    {
         // ສົມມຸດວ່າມີ Model ຊື່ StudentPreviousLocation
         return $this->hasMany(StudentPreviousLocation::class, 'district_id', 'district_id');
    }
}