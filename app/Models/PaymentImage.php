<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PaymentImage extends Model
{
    use HasFactory;

    protected $table = 'payment_images';
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'payment_id',
        'image_path',
        'image_type',
        'file_size',
        'mime_type',
        'upload_date',
    ];

    protected $casts = [
        'upload_date' => 'datetime',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getImageTypeLabel(): string
    {
        return match ($this->image_type) {
            'receipt' => 'ໃບບິນ',
            'transfer_slip' => 'ໃບໂອນເງິນ',
            'other' => 'ອື່ນໆ',
            default => $this->image_type,
        };
    }

    /**
     * Scopes
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('image_type', $type);
    }

    public function scopeReceipts($query)
    {
        return $query->where('image_type', 'receipt');
    }

    public function scopeTransferSlips($query)
    {
        return $query->where('image_type', 'transfer_slip');
    }

    /**
     * Helper Methods
     */
    public function exists(): bool
    {
        return Storage::disk('public')->exists($this->image_path);
    }

    public function delete(): bool
    {
        // Delete file from storage
        if ($this->exists()) {
            Storage::disk('public')->delete($this->image_path);
        }

        // Delete record from database
        return parent::delete();
    }

    /**
     * Static Methods
     */
    public static function getImageTypeOptions(): array
    {
        return [
            'receipt' => 'ໃບບິນ',
            'transfer_slip' => 'ໃບໂອນເງິນ',
            'other' => 'ອື່ນໆ',
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($image) {
            if (!$image->upload_date) {
                $image->upload_date = now();
            }

            if (!$image->image_type) {
                $image->image_type = 'receipt';
            }
        });

        static::deleting(function ($image) {
            // Delete file when model is deleted
            if ($image->exists()) {
                Storage::disk('public')->delete($image->image_path);
            }
        });
    }
}