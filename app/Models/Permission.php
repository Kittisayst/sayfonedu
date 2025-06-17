<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // ສຳຄັນ: Import BelongsToMany

class Permission extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * ໂດຍ default, Laravel ຈະໃຊ້ຊື່ class ໃນຮູບແບບ snake_case ແລະ ເປັນ plural ('permissions').
     * ຖ້າຊື່ຕາຕະລາງໃນ migration ຂອງທ່ານກົງກັບ default ນີ້, ແຖວນີ້ບໍ່ຈຳເປັນຕ້ອງໃສ່.
     *
     * protected $table = 'permissions';
     */

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     * ໂດຍ default, Laravel ຄິດວ່າແມ່ນ 'id'. ເຮົາຕ້ອງກຳນົດໃໝ່ຖ້າໃຊ້ຊື່ອື່ນ.
     *
     * @var string
     */
    protected $primaryKey = 'permission_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     * ຖ້າ migration ໃຊ້ $table->timestamps(), ຄ່າ default ແມ່ນ true.
     *
     * @var bool
     */
    public $timestamps = true; // Default ແມ່ນ true, ໃສ່ຫຼືບໍ່ໃສ່ກໍ່ໄດ້ຖ້າຕ້ອງການ timestamps

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable (ເຊັ່ນ: ເວລາສ້າງຜ່ານ Request).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_name',
        'description',
    ];

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ບໍ່ຕ້ອງການໃຫ້ສະແດງເວລາປ່ຽນ Model ເປັນ array ຫຼື JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // ຕາມປົກກະຕິອາດຈະບໍ່ມີຫຍັງໃນນີ້ສຳລັບ Model ທົ່ວໄປ
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting) ສຳລັບຟີລດ໌ຕ່າງໆ.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ຕອນນີ້ຍັງບໍ່ມີຫຍັງທີ່ຕ້ອງການ cast ເປັນພິເສດ
    ];

    /**
     * ສ້າງ Relationship ແບບ Many-to-Many ກັບ Model Role.
     * ບອກວ່າ Permission ໜຶ່ງອັນສາມາດເປັນຂອງ Role ໃດແດ່.
     */
    public function roles(): BelongsToMany
    {
        // belongsToMany(RelatedModel, pivot_table_name, foreign_pivot_key, related_pivot_key)
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
        // ສົມມຸດວ່າ:
        // - ມີ Model ຊື່ Role ຢູ່ App\Models\Role
        // - ຕາຕະລາງເຊື່ອມໂຍງຊື່ 'role_permissions' (D4)
        // - Foreign key ໃນຕາຕະລາງເຊື່ອມໂຍງທີ່ອ້າງອີງເຖິງ Permission ແມ່ນ 'permission_id'
        // - Foreign key ໃນຕາຕະລາງເຊື່ອມໂຍງທີ່ອ້າງອີງເຖິງ Role ແມ່ນ 'role_id'
    }
}