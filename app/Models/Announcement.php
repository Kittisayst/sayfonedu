<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Builder;              // Import Builder for scope
use Carbon\Carbon;                                     // Import Carbon for date comparison

class Announcement extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'announcements' ໂດຍ default.
     */
    // protected $table = 'announcements';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'announcement_id';

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
        'title',
        'content',
        'start_date',
        'end_date',
        'target_group',
        'is_pinned',
        'attachment',
        'created_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d', // ແປງເປັນ Date object
        'end_date' => 'date:Y-m-d',   // ແປງເປັນ Date object
        'is_pinned' => 'boolean',    // ແປງເປັນ Boolean
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ປະກາດນີ້ຖືກສ້າງໂດຍ User ຄົນດຽວ (Creator).
     */
    public function creator(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ສ້າງ
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    // ========================================================================
    // Query Scopes (ຕົວຢ່າງ)
    // ========================================================================

    /**
     * Scope a query to only include active announcements for today.
     * Active means today's date is between start_date and end_date (inclusive),
     * or start_date is today or earlier and end_date is null.
     */
    public function scopeActive(Builder $query): Builder
    {
        $today = Carbon::today()->toDateString();

        return $query->where(function ($q) use ($today) {
                        // Starts on or before today (or has no start date)
                        $q->where('start_date', '<=', $today)
                          ->orWhereNull('start_date');
                    })
                    ->where(function ($q) use ($today) {
                        // Ends on or after today, or has no end date
                        $q->where('end_date', '>=', $today)
                          ->orWhereNull('end_date');
                    });
    }

    /**
     * Scope a query to filter by target group(s).
     * Can accept a single group string or an array of groups.
     */
    public function scopeForGroup(Builder $query, string|array $group): Builder
    {
        if (is_array($group)) {
            // If multiple groups provided (e.g., specific role + 'all')
            return $query->whereIn('target_group', $group);
        }
        // If single group provided
        return $query->where('target_group', $group);
    }

    // Note: Application logic should validate date sequence (end_date >= start_date) if necessary.
}