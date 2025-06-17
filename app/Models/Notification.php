<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Builder;              // Import Builder for Scope

class Notification extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'notifications' ໂດຍ default.
     */
    // protected $table = 'notifications';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'notification_id';

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
        'user_id', // Foreign key
        'title',
        'content',
        'notification_type',
        'related_id',
        'is_read',
        'read_at',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',     // Cast ເປັນ boolean
        'read_at' => 'datetime',    // Cast ເປັນ datetime object (Carbon)
        'related_id' => 'integer',  // Cast ເປັນ integer (ເຖິງແມ່ນວ່າໃນ DB ເປັນ BigInt)
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * Relationship: Notification ນີ້ເປັນຂອງ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: ເອົາ object ທີ່ກ່ຽວຂ້ອງ (Polymorphic - ຈັດການເອງ).
     * ນີ້ເປັນພຽງຕົວຢ່າງພື້ນຖານ, ການ implement ຕົວຈິງອາດຈະສັບຊ້ອນກວ່ານີ້.
     *
     * @return Model|null
     */
    public function relatedObject()
    {
        if ($this->notification_type && $this->related_id) {
            // ສ້າງ map ເພື່ອແປງ notification_type ເປັນຊື່ Class ຂອງ Model
            $modelMap = [
                'new_message'       => Message::class,
                'request_update'    => Request::class, // ສົມມຸດມີ Model Request
                'request_approved'  => Request::class,
                'request_rejected'  => Request::class,
                'new_announcement'  => Announcement::class,
                'fee_due'           => StudentFee::class, // ສົມມຸດມີ Model StudentFee
                // ເພີ່ມປະເພດອື່ນໆ ແລະ Model ທີ່ກ່ຽວຂ້ອງຕາມຕ້ອງການ...
            ];

            if (isset($modelMap[$this->notification_type])) {
                $relatedModelClass = $modelMap[$this->notification_type];
                // ໃຊ້ find() ຫຼື findOrFail() ເພື່ອດຶງ object ທີ່ກ່ຽວຂ້ອງ
                // ລະວັງກໍລະນີ object ຕົ້ນສະບັບຖືກລຶບໄປແລ້ວ find() ຈະໄດ້ null
                return $relatedModelClass::find($this->related_id);
            }
        }
        return null;
    }


    // ========================================================================
    // Query Scopes
    // ========================================================================

    /**
     * Scope ເພື່ອເອົາສະເພາະ Notification ທີ່ຍັງບໍ່ໄດ້ອ່ານ.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}