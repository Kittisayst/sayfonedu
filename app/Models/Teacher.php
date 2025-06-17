<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany
use Illuminate\Database\Eloquent\Relations\HasOne;

class Teacher extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Defaults to: 'teachers'
     */
    // protected $table = 'teachers';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'teacher_id';

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
        'teacher_code',
        'first_name_lao',
        'last_name_lao',
        'first_name_en',
        'last_name_en',
        'gender',
        'date_of_birth',
        'national_id',
        'phone',
        'alternative_phone',
        'email',
        'village_id',
        'district_id',
        'province_id',
        'address',
        'highest_education',
        'specialization',
        'employment_date',
        'contract_type',
        'status',
        'profile_image',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d', // ແປງເປັນ object Carbon/Date, ກຳນົດ format ຖ້າຕ້ອງການ
        'employment_date' => 'date:Y-m-d',
    ];

    /**
     * Relationship: Teacher ໜຶ່ງຄົນຂຶ້ນກັບ User account ດຽວ.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id', 'related_id')
            ->where('user_type', 'teacher');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນອາໄສຢູ່ Village ດຽວ.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນອາໄສຢູ່ District ດຽວ.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນອາໄສຢູ່ Province ດຽວ.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນມີຫຼາຍ TeacherDocuments.
     */
    public function documents(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ TeacherDocument
        return $this->hasMany(TeacherDocument::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນອາດຈະເປັນຄູປະຈຳຫ້ອງຂອງຫຼາຍ Classes.
     */
    public function homeroomClasses(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ SchoolClass (ປ່ຽນຈາກ Classes)
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id', 'teacher_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນສອນຫຼາຍ ClassSubjects.
     */
    public function classSubjects(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ ClassSubject
        return $this->hasMany(ClassSubject::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນມີຫຼາຍ Schedule entries.
     */
    public function schedules(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Schedule
        return $this->hasMany(Schedule::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Relationship: Teacher ໜຶ່ງຄົນບັນທຶກຫຼາຍ StudentBehaviorRecords.
     */
    public function recordedBehaviorRecords(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ StudentBehaviorRecord
        return $this->hasMany(StudentBehaviorRecord::class, 'teacher_id', 'teacher_id');
    }

    /**
      * Relationship: User ທີ່ຜູກກັບ Teacher ນີ້ ໃຫ້ຄະແນນຫຼາຍ Grades.
      * ໝາຍເຫດ: ອັນນີ້ເຊື່ອມໂຍງຜ່ານ user_id, ບໍ່ແມ່ນ teacher_id ໂດຍກົງ.
      */
    public function givenGrades(): HasMany
    {
        // ສົມມຸດວ່າມີ Model ຊື່ Grade
        // This assumes the 'graded_by' column in the 'grades' table stores the 'user_id'
        return $this->hasMany(Grade::class, 'graded_by', 'user_id');
    }

    /**
     * ຄວາມສຳພັນກັບຜູ້ໃຊ້
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name_lao} {$this->last_name_lao}";
    }

    /**
     * ສົ່ງຄ່າກັບຊື່ເຕັມຂອງຄູສອນ
     */
    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s (%s) - %s',
            $this->full_name,
            $this->teacher_code,
            $this->specialization ?? 'ບໍ່ມີຂໍ້ມູນສະເພາະ'
        );
    }

}