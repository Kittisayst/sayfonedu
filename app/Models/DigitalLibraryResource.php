<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class DigitalLibraryResource extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'digital_library_resources' ໂດຍ default.
     */
    // protected $table = 'digital_library_resources';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'resource_id';

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
        'author',
        'publisher',
        'publication_year',
        'resource_type',
        'category',
        'description',
        'file_path',
        'file_size',
        'thumbnail',
        'is_active',
        'added_by', // Foreign key
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publication_year' => 'integer', // Cast year ເປັນ integer
        'file_size' => 'integer',       // Cast file_size ເປັນ integer
        'is_active' => 'boolean',     // Cast is_active ເປັນ boolean
    ];

    // ========================================================================
    // Relationships
    // ========================================================================

    /**
     * Relationship: ຊັບພະຍາກອນນີ້ຖືກເພີ່ມໂດຍ User ຄົນດຽວ (Adder).
     */
    public function adder(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model ສຳລັບຜູ້ເພີ່ມຂໍ້ມູນ
        return $this->belongsTo(User::class, 'added_by', 'user_id');
    }

    /**
     * Relationship: ຊັບພະຍາກອນນີ້ມີຫຼາຍ Access Logs.
     */
    public function accessLogs(): HasMany
    {
        // ສົມມຸດ Model ຊື່ DigitalResourceAccess
        // hasMany(RelatedModel, foreign_key_on_related_model, local_key_on_this_model)
        return $this->hasMany(DigitalResourceAccess::class, 'resource_id', 'resource_id');
    }

    // Note: Application logic should validate publication_year and file_size if necessary.
}