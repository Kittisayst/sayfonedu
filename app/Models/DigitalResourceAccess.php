<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class DigitalResourceAccess extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ຕ້ອງກຳນົດເອງ ເພາະ default ຂອງ Laravel ('digital_resource_accesses') ບໍ່ກົງກັບ Migration.
     *
     * @var string
     */
    protected $table = 'digital_resource_access';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'access_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     * ເນື່ອງຈາກຕາຕະລາງ Log ນີ້ມີແຕ່ created_at (ຈາກ Migration), ເຮົາຈະປິດ updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * ໂດຍທົ່ວໄປ Log ອາດຈະບໍ່ mass assign, ແຕ່ກຳນົດໄວ້ກໍ່ໄດ້.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'resource_id', // Foreign key
        'user_id', // Foreign key
        'access_time',
        'access_type',
        'device_info',
        'ip_address',
        // created_at ໂດຍທົ່ວໄປບໍ່ຄວນ mass assign
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'access_time' => 'datetime', // Cast ເປັນ datetime object (Carbon)
        'created_at' => 'datetime',  // Cast created_at ທີ່ເຮົາກຳນົດເອງ
    ];

    /**
     * ກຳນົດຊື່ຟີລດ໌ created_at ໃຫ້ Laravel ຮູ້ຈັກ ເມື່ອ $timestamps = false.
     * ອາດຈະບໍ່ຈຳເປັນ ຖ້າຊື່ຟີລດ໌ກົງກັບ default ('created_at').
     */
    const CREATED_AT = 'created_at';


    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: Log ການເຂົ້າເຖິງນີ້ ກ່ຽວຂ້ອງກັບ Resource ອັນດຽວ.
     */
    public function resource(): BelongsTo
    {
        // ສົມມຸດ Model ຊື່ DigitalLibraryResource
        return $this->belongsTo(DigitalLibraryResource::class, 'resource_id', 'resource_id');
    }

    /**
     * Relationship: Log ການເຂົ້າເຖິງນີ້ ເປັນຂອງ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}