<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class StudentSibling extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'student_siblings' ໂດຍ default.
     */
    // protected $table = 'student_siblings';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'sibling_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼັກຫຼື ບໍ່.
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
        'student_id', // Foreign key
        'sibling_student_id', // Foreign key
        'relationship',
    ];

    /**
     * Relationship: ເອົານັກຮຽນຄົນຫຼັກ (ທີ່ຖືກອ້າງອີງໂດຍ student_id).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Relationship: ເອົານັກຮຽນທີ່ເປັນພີ່ນ້ອງ (ທີ່ຖືກອ້າງອີງໂດຍ sibling_student_id).
     */
    public function sibling(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'sibling_student_id', 'student_id');
    }

    // ໝາຍເຫດ: ການດຶງຂໍ້ມູນພີ່ນ້ອງທັງໝົດຂອງນັກຮຽນຄົນໃດໜຶ່ງ
    // ໂດຍທົ່ວໄປຈະໃຊ້ Relationship ແບບ Many-to-Many ที่กำหนดไว้ใน Student Model
    // (ເຊັ່ນ function siblings() ແລະ siblingOf() ທີ່ເຮົາສ້າງໄວ້ໃນ Student Model).
    // Model StudentSibling ນີ້ ໂດຍຫຼັກແລ້ວແມ່ນໃຊ້ເພື່ອເປັນຕົວແທນຂອງ record ການເຊື່ອມໂຍງນີ້.
}