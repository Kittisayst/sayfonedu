<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Message extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'messages' ໂດຍ default.
     */
    // protected $table = 'messages';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'message_id';

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
        'sender_id', // Foreign key
        'receiver_id', // Foreign key
        'subject',
        'message_content',
        'read_status',
        'read_at',
        'attachment',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_status' => 'boolean', // Cast ເປັນ boolean
        'read_at' => 'datetime', // Cast ເປັນ datetime object (Carbon)
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: ຂໍ້ຄວາມນີ້ຖືກສົ່ງໂດຍ User ຄົນດຽວ (Sender).
     */
    public function sender(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ສົ່ງ
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    /**
     * Relationship: ຂໍ້ຄວາມນີ້ຖືກສົ່ງຫາ User ຄົນດຽວ (Receiver).
     */
    public function receiver(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ຮັບ
        return $this->belongsTo(User::class, 'receiver_id', 'user_id');
    }

    // Note: Application logic could prevent sender_id = receiver_id if needed.
}