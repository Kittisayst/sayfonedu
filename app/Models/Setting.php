<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'settings' ໂດຍ default.
     */
    // protected $table = 'settings';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'setting_id';

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
        'setting_key',
        'setting_value',
        'setting_group',
        'description',
        'is_system',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean', // Cast is_system ເປັນ boolean
        // ຖ້າຫາກ setting_value ມັກຈະເກັບຂໍ້ມູນແບບ JSON, ສາມາດເພີ່ມ:
        // 'setting_value' => 'array',
    ];

    // ໂດຍທົ່ວໄປ, ຕາຕະລາງ Settings ຈະບໍ່ມີ Relationships ແບບ BelongsTo ອອກໄປຫາຕາຕະລາງອື່ນ
    // ແຕ່ຕາຕະລາງ (ຫຼື code) ອື່ນໆຈະອ່ານຄ່າຈາກຕາຕະລາງນີ້.
}