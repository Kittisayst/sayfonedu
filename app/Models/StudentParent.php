<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// Model ສໍາລັບຕາຕະລາງ 'parents' (D25), ໃຊ້ຊື່ Class StudentParents ຕາມຄຳຂໍ
class StudentParent extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ຕ້ອງກຳນົດເອງ ເພາະຊື່ Model ບໍ່ກົງກັບຊື່ຕາຕະລາງ 'parents'.
     *
     * @var string
     */
    protected $table = 'parents'; // ບອກໃຫ້ໃຊ້ຕາຕະລາງ 'parents'

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'parent_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * (ຄືກັນກັບຕອນເປັນ Guardian)
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name_lao', 'last_name_lao', 'first_name_en', 'last_name_en',
        'gender', 'date_of_birth', 'national_id', 'occupation', 'workplace',
        'education_level', 'income_level', 'phone', 'alternative_phone', 'email',
        'village_id', 'district_id', 'province_id', 'address',
        'profile_image',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
    ];

    // Relationships (ຄືກັນກັບຕອນເປັນ Guardian)

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id', 'related_id')
            ->where('user_type', 'parent');
    }

    public function getFullName(){
        return "$this->first_name_lao $this->last_name_lao";
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    /**
     * Get the full name of the parent.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name_lao . ' ' . $this->last_name_lao;
    }

    /**
     * Get the display name of the parent with additional information.
     */
    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s (%s) - %s',
            $this->full_name,
            $this->phone,
            $this->occupation ?? 'ບໍ່ມີຂໍ້ມູນອາຊີບ'
        );
    }

    /**
     * Relationship: ຜູ້ປົກຄອງນີ້ (Parent/Guardian) ກ່ຽວຂ້ອງກັບຫຼາຍ Students.
     * ຜ່ານຕາຕະລາງ 'student_parent' (D26).
     */
    public function students(): BelongsToMany
    {
        // ສົມມຸດວ່າ Model ສໍາລັບຕາຕະລາງ D26 ແມ່ນ StudentParent
        return $this->belongsToMany(Student::class, 'student_parent', 'parent_id', 'student_id')
                    ->using(StudentParentPivot::class) // ບອກໃຫ້ໃຊ້ Pivot Model ຊື່ StudentParentPivot
                    ->withPivot('relationship', 'is_primary_contact', 'has_custody')
                    ->withTimestamps();
    }
}