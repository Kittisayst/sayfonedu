<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PaymentImage extends Model
{
    use HasFactory;

    /**
     * ກຳນົດຊື່ primary key ຂອງຕາຕະລາງ
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * ຟີລທີ່ອະນຸຍາດໃຫ້ປ້ອນຂໍ້ມູນແບບ Mass Assignment
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'image_path',
        'upload_date',
    ];

    /**
     * ຟີລທີ່ຄວນແປງເປັນ dates
     *
     * @var array<int, string>
     */
    protected $dates = [
        'upload_date',
    ];

    /**
     * Accessors and Mutators
     */

    /**
     * ລຶບຮູບພາບຈາກ storage ເມື່ອລຶບ record
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($paymentImage) {
            // ລຶບໄຟລ໌ອອກຈາກ storage
            if ($paymentImage->image_path && Storage::exists($paymentImage->image_path)) {
                Storage::delete($paymentImage->image_path);
            }
        });
    }

    /**
     * ຄວາມສຳພັນກັບຕາຕະລາງ Payment
     *
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * ດຶງ URL ສຳລັບຮູບພາບ
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return '';
        }

        // ຖ້າພາບເກັບໄວ້ໃນ disk ເຊັ່ນ public, s3, ...
        return Storage::url($this->image_path);
    }

    /**
     * ກວດຊະນິດຂອງໄຟລ໌ຮູບພາບ
     *
     * @return string
     */
    public function getFileTypeAttribute(): string
    {
        if (empty($this->image_path)) {
            return '';
        }

        $extension = pathinfo($this->image_path, PATHINFO_EXTENSION);

        return strtoupper($extension);
    }

    /**
     * ຂະໜາດຂອງໄຟລ໌ໃນຮູບແບບທີ່ອ່ານງ່າຍ
     *
     * @return string
     */
    public function getFileSizeFormattedAttribute(): string
    {
        if (empty($this->image_path) || !Storage::exists($this->image_path)) {
            return '0 KB';
        }

        $bytes = Storage::size($this->image_path);

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * ສະຖານະຮູບພາບ (ມີຢູ່ໃນ storage ຫຼື ບໍ່)
     *
     * @return bool
     */
    public function getExistsAttribute(): bool
    {
        return !empty($this->image_path) && Storage::exists($this->image_path);
    }
}
