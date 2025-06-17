<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // ເອົາ comment ອອກ ຖ້າໃຊ້ລະບົບ

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ຖ້າໃຊ້ Sanctum ສຳລັບ API Authentication

// Import Models ສຳລັບ Relationships
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

// ປ່ຽນຊື່ Class ເປັນ UserModel ຖ້າຫາກຊື່ Parent ຂັດກັບ Keyword ຫຼື Class ອື່ນ
// use App\Models\Parent as ParentModel;

class User extends Authenticatable implements FilamentUser // implements MustVerifyEmail // ເອົາ comment ອອກ ຖ້າໃຊ້ລະບົບ
{
    // Traits ທີ່ Laravel ໃຊ້ (ອາດຈະມີ HasApiTokens ຖ້າໃຊ້ Sanctum)
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * ຊື່ຕາຕະລາງ. Laravel ຈະໃຊ້ 'users' ໂດຍ default.
     * protected $table = 'users';
     */

    /**
     * Primary Key. Laravel ຄິດວ່າແມ່ນ 'id' ໂດຍ default.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     *
     * @var bool
     */
    public $timestamps = true; // Default ແມ່ນ true

    /**
     * ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password', // ລະວັງການ mass assign password, ຄວນຜ່ານ mutator ຫຼື hash ເອງ
        'phone',
        'role_id',
        'status',
        'profile_image',
        'user_type',
        'related_id',
    ];

    /**
     * ຟີລດ໌ທີ່ຄວນເຊື່ອງໄວ້ ເວລາປ່ຽນເປັນ array ຫຼື JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token', // ຟີລດ໌ມາດຕະຖານຂອງ Laravel
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // ຟີລດ໌ມາດຕະຖານ, ເກັບໄວ້ຖ້າໃຊ້ email verification
        'password' => 'hashed', // ບອກ Laravel ໃຫ້ hash password ອັດຕະໂນມັດ (Laravel 10+)
        'last_login' => 'datetime', // ແປງເປັນ object Carbon
    ];

    /**
     * Relationship: User ໜຶ່ງຄົນຂຶ້ນກັບ Role ດຽວ.
     */
    public function role(): BelongsTo
    {
        // belongsTo(RelatedModel, foreign_key_on_this_model, owner_key_on_related_model)
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->username ?? 'User',
        );
    }

    /**
     * ກວດສອບສິດໃນການເຂົ້າເຖິງແຜງຄວບຄຸມ Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // ກວດສອບວ່າຜູ້ໃຊ້ມີສະຖານະ active ຫຼືບໍ່
        if ($this->status !== 'active') {
            return false;
        }

        // ກວດສອບວ່າຜູ້ໃຊ້ມີບົດບາດຫຼືບໍ່
        if (!$this->role) {
            return false;
        }

        // ກຳນົດບົດບາດທີ່ອະນຸຍາດໃຫ້ເຂົ້າເຖິງແຜງຄວບຄຸມ
        $allowedRoles = config('roles.access_panels', ['admin', 'school_admin', 'finance_staff']);

        // ກວດສອບວ່າບົດບາດຂອງຜູ້ໃຊ້ຢູ່ໃນລາຍຊື່ທີ່ອະນຸຍາດຫຼືບໍ່
        return in_array($this->role->role_name, $allowedRoles);
    }

    /**
     * Relationship: User ໜຶ່ງຄົນມີຫຼາຍ User Activities.
     */
    public function userActivities(): HasMany
    {
        // ຟີລດ໌ທີສອງ ແລະ ສາມ ປົກກະຕິ Laravel ສາມາດ detect ໄດ້ເອງ ຖ້າໃຊ້ convention
        // return $this->hasMany(UserActivity::class);
        return $this->hasMany(UserActivity::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: User ໜຶ່ງຄົນອາດຈະມີຫຼາຍ Biometric Data.
     */
    public function biometricData(): HasMany
    {
        return $this->hasMany(BiometricData::class, 'user_id', 'user_id');
    }

    /**
     * ຄວາມສຳພັນກັບຄົນນຳໃຊ້
     */
    public function related()
    {
        return match($this->user_type) {
            'teacher' => $this->belongsTo(Teacher::class, 'related_id', 'teacher_id'),
            'parent' => $this->belongsTo(StudentParent::class, 'related_id', 'parent_id'),
            'student' => $this->belongsTo(Student::class, 'related_id', 'student_id'),
            default => null,
        };
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຄູສອນຫຼືບໍ່
     */
    public function isTeacher(): bool
    {
        return $this->user_type === 'teacher' && $this->related_id !== null;
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນຜູ້ປົກຄອງຫຼືບໍ່
     */
    public function isParent(): bool
    {
        return $this->user_type === 'parent' && $this->related_id !== null;
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ເປັນນັກຮຽນຫຼືບໍ່
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student' && $this->related_id !== null;
    }

    /**
     * ສົ່ງຄ່າກັບຂໍ້ມູນທີ່ກ່ຽວຂ້ອງກັບປະເພດຜູ້ໃຊ້
     */
    public function getRelatedData()
    {
        return $this->related;
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ມີບົດບາດສະເພາະຫຼືບໍ່
     */
    public function hasRole(string $role): bool
    {
        return $this->role && $this->role->role_name === $role;
    }

    /**
     * ກວດສອບວ່າຜູ້ໃຊ້ມີບົດບາດໃດນຶ່ງຈາກຫຼາຍບົດບາດທີ່ກຳນົດຫຼືບໍ່
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->role && in_array($this->role->role_name, $roles);
    }

    /**
     * ກຳນົດ field ທີ່ຈະໃຊ້ login
     */
    public function username()
    {
        return 'username';
    }

    protected function profileImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->profile_image
                ? Storage::url($this->profile_image)
                : asset('images/default-profile.png'),
        );
    }
}
