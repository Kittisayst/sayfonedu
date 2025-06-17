<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentParentPivot extends Pivot
{
    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     *
     * @var string
     */
    protected $table = 'student_parent';

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
        'student_id',
        'parent_id',
        'relationship',
        'is_primary_contact',
        'has_custody',
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_primary_contact' => 'boolean',
        'has_custody' => 'boolean',
    ];
} 