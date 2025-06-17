<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    use LogsActivity;

    /**
     * ຊື່ຕາຕະລາງ. Defaults to: 'students'
     */
    // protected $table = 'students';

    /**
     * Primary Key.
     *
     * @var string
     */
    protected $primaryKey = 'student_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps ຫຼື ບໍ່.
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
        'student_code',
        'first_name_lao',
        'last_name_lao',
        'first_name_en',
        'last_name_en',
        'nickname',
        'gender',
        'date_of_birth',
        'nationality_id',
        'religion_id',
        'ethnicity_id',
        'village_id',
        'district_id',
        'province_id',
        'current_address',
        'profile_image',
        'blood_type',
        'status',
        'admission_date',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'admission_date' => 'date:Y-m-d',
    ];

    // ========================================================================
    // Relationships: Belongs To (Many-to-One / One-to-One Inverse)
    // ========================================================================

    /**
     * ເອົາບັນຊີຜູ້ໃຊ້ທີ່ຜູກກັບນັກຮຽນ (ຖ້າມີ).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'related_id')
            ->where('user_type', 'student');
    }

    /**
     * ຮັບຊື່ເຕັມຂອງນັກຮຽນເປັນພາສາລາວ
     *
     * @return string ຊື່ເຕັມຂອງນັກຮຽນ
     */
    public function getFullName(): string
    {
        $gender = $this->gender == "male" ? "ທ້າວ" : "ນາງ";
        return "$gender $this->first_name_lao $this->last_name_lao";
    }

    /**
     * ຮັບຊື່ເຕັມຂອງນັກຮຽນເປັນພາສາອັງກິດ
     *
     * @return string|null ຊື່ເຕັມຂອງນັກຮຽນເປັນພາສາອັງກິດ ຫຼື null ຖ້າບໍ່ມີຂໍ້ມູນ
     */
    public function getFullNameEng(): ?string
    {
        if (empty($this->first_name_eng) || empty($this->last_name_eng)) {
            return null;
        }

        return $this->first_name_eng . ' ' . $this->last_name_eng;
    }

    public function getDateOfBirth()
    {
        return $this->date_of_birth ? date('d/m/Y', strtotime($this->date_of_birth)) : 'ບໍ່ມີຂໍ້ມູນ';
    }

    /**
     * ເອົາສັນຊາດຂອງນັກຮຽນ.
     */
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class, 'nationality_id', 'nationality_id');
    }

    /**
     * ເອົາສາສະໜາຂອງນັກຮຽນ.
     */
    public function religion(): BelongsTo
    {
        return $this->belongsTo(Religion::class, 'religion_id', 'religion_id');
    }

    /**
     * ເອົາຊົນເຜົ່າຂອງນັກຮຽນ.
     */
    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class, 'ethnicity_id', 'ethnicity_id');
    }

    /**
     * ເອົາບ້ານທີ່ນັກຮຽນອາໄສຢູ່.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id', 'village_id');
    }

    /**
     * ເອົາເມືອງທີ່ນັກຮຽນອາໄສຢູ່.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'district_id');
    }

    /**
     * ເອົາແຂວງທີ່ນັກຮຽນອາໄສຢູ່.
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'province_id');
    }

    // ========================================================================
    // Relationships: Has Many (One-to-Many)
    // ========================================================================
    /**
     * ເອົາຂໍ້ມູນການລົງທະບຽນທັງໝົດຂອງນັກຮຽນ.
     */
    public function enrollments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentEnrollment
        return $this->hasMany(StudentEnrollment::class, 'student_id', 'student_id');
    }

    // ສຳລັບດຶງການລົງທະບຽນປັດຈຸບັນ
    public function currentEnrollment()
    {
        // ສົມມຸດວ່າມີ field ປີການສຶກສາ ແລະ ເທີມ
        $currentYear = date('Y'); // ຫຼື logic ອື່ນເພື່ອກຳນົດປີການສຶກສາປັດຈຸບັນ

        return $this->enrollments()
            ->where('academic_year', $currentYear)
            ->latest() // ເອົາລາຍການຫຼ້າສຸດຖ້າມີຫຼາຍລາຍການ
            ->first();
    }

    // ໃນ Student Model
    public function getCurrentClassName()
    {
        $enrollment = $this->enrollments
            ->where('status', 'active')
            ->first();

        return $enrollment?->schoolClass?->class_name ?? 'ບໍ່ມີຂໍ້ມູນ';
    }

    // ຫຼື ເປັນ accessor:
    public function getCurrentClassNameAttribute()
    {
        $enrollment = $this->enrollments
            ->where('status', 'active')
            ->first();

        return $enrollment?->schoolClass?->class_name ?? 'ບໍ່ມີຂໍ້ມູນ';
    }

    /**
     * ເອົາຂໍ້ມູນຄະແນນທັງໝົດຂອງນັກຮຽນ.
     */
    public function grades(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Grade
        return $this->hasMany(Grade::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຂໍ້ມູນການຂາດ-ມາ ທັງໝົດຂອງນັກຮຽນ.
     */
    public function attendanceRecords(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Attendance
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາເອກະສານທັງໝົດຂອງນັກຮຽນ.
     */
    public function documents(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentDocument
        return $this->hasMany(StudentDocument::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຂໍ້ມູນສຸຂະພາບຂອງນັກຮຽນ.
     */
    public function healthRecords(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentHealthRecord
        return $this->hasMany(StudentHealthRecord::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາລາຍຊື່ຜູ້ຕິດຕໍ່ສຸກເສີນຂອງນັກຮຽນ.
     */
    public function emergencyContacts(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentEmergencyContact
        return $this->hasMany(StudentEmergencyContact::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາປະຫວັດການສຶກສາກ່ອນໜ້າຂອງນັກຮຽນ.
     */
    public function previousEducation(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentPreviousEducation
        return $this->hasMany(StudentPreviousEducation::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຜົນງານ/ລາງວັນຂອງນັກຮຽນ.
     */
    public function achievements(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentAchievement
        return $this->hasMany(StudentAchievement::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາບັນທຶກພຶດຕິກຳຂອງນັກຮຽນ.
     */
    public function behaviorRecords(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentBehaviorRecord
        return $this->hasMany(StudentBehaviorRecord::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຂໍ້ມູນຄວາມຕ້ອງການພິເສດຂອງນັກຮຽນ.
     */
    public function specialNeeds(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentSpecialNeed
        return $this->hasMany(StudentSpecialNeed::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາລາຍການຄ່າທຳນຽມຂອງນັກຮຽນ.
     */
    // public function fees(): HasMany
    // {
    //     // ສົມມຸດ Model ຊື່ StudentFee
    //     return $this->hasMany(StudentFee::class, 'student_id', 'student_id');
    // }

    /**
     * ເອົາປະຫວັດການຈ່າຍເງິນຂອງນັກຮຽນ (ທີ່ອ້າງອີງໂດຍກົງ).
     */
    public function payments(): HasMany
    {
        // ສົມມຸດ Model ຊື່ Payment
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາສ່ວນຫຼຸດທີ່ນັກຮຽນໄດ້ຮັບ.
     */
    public function discounts(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentDiscount
        return $this->hasMany(StudentDiscount::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຂໍ້ມູນທີ່ຢູ່ເກົ່າຂອງນັກຮຽນ.
     */
    public function previousLocations(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentPreviousLocation
        return $this->hasMany(StudentPreviousLocation::class, 'student_id', 'student_id');
    }

    /**
     * ເອົາຂໍ້ມູນຄວາມສົນໃຈຂອງນັກຮຽນ.
     */
    public function interests(): HasMany
    {
        // ສົມມຸດ Model ຊື່ StudentInterest
        return $this->hasMany(StudentInterest::class, 'student_id', 'student_id');
    }

    // ========================================================================
    // Relationships: Belongs To Many (Many-to-Many)
    // ========================================================================

    /**
     * ເອົາຜູ້ປົກຄອງທີ່ກ່ຽວຂ້ອງກັບນັກຮຽນ.
     */
    public function parents()
    {
        return $this->belongsToMany(StudentParent::class, 'student_parent', 'student_id', 'parent_id')
            ->withPivot('relationship', 'is_primary_contact', 'has_custody')
            ->withTimestamps();
    }

    /**
     * ເອົາກິດຈະກຳນອກຫຼັກສູດທີ່ນັກຮຽນເຂົ້າຮ່ວມ.
     */
    public function activities(): BelongsToMany
    {
        // ສົມມຸດ Model ຊື່ ExtracurricularActivity
        return $this->belongsToMany(ExtracurricularActivity::class, 'student_activities', 'student_id', 'activity_id')
            ->withPivot('join_date', 'status', 'performance', 'notes')
            ->withTimestamps();
    }

    /**
     * Relationship: ເອົາພີ່ນ້ອງຂອງນັກຮຽນ (Self-referencing Many-to-Many).
     * ອັນນີ້ແມ່ນເອົາລາຍຊື່ຄົນທີ່ນັກຮຽນຄົນນີ້ເປັນ 'student_id' ໃນຕາຕະລາງເຊື່ອມໂຍງ.
     */

    // ຄວາມສຳພັນກັບພີ່ນ້ອງ (ເຊື່ອມຜ່ານຕາຕະລາງ student_siblings)
    public function siblingRelationships()
    {
        return $this->hasMany(StudentSibling::class, 'student_id');
    }

    public function siblings(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'student_siblings',
            'student_id',
            'sibling_student_id'
        )->withPivot('relationship')->withTimestamps();
    }

    // ສຳລັບເຊັກວ່າມີພີ່ນ້ອງຫຼືບໍ່
    public function hasSibling(Student $student): bool
    {
        return $this->siblings()->where('sibling_student_id', $student->student_id)->exists();
    }

    // ເພີ່ມ method ສຳລັບການສະແດງຊື່ຜູ້ປົກຄອງ
    public function getFullNameAttribute(): string
    {
        return $this->first_name_lao . ' ' . $this->last_name_lao;
    }

    // ເພີ່ມ method ສຳລັບການສະແດງຂໍ້ມູນຜູ້ປົກຄອງໃນຮູບແບບທີ່ງ່າຍຕໍ່ການເລືອກ
    public function getDisplayNameAttribute(): string
    {
        return sprintf(
            '%s (%s) - %s',
            $this->full_name,
            $this->phone,
            $this->occupation ?? 'ບໍ່ມີຂໍ້ມູນອາຊີບ'
        );
    }

    // ເພີ່ມ method ສຳລັບການຄົ້ນຫາຜູ້ປົກຄອງ
    public static function search($query)
    {
        return static::where('first_name_lao', 'like', "%{$query}%")
            ->orWhere('last_name_lao', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('national_id', 'like', "%{$query}%");
    }
}
