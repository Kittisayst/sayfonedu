<?php

namespace App\Models;

use App\Enums\LogLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class SystemLog extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ Model ນີ້ອ້າງອີງເຖິງ.
     * Laravel ຈະໃຊ້ 'system_logs' ໂດຍ default.
     */
    // protected $table = 'system_logs';

    /**
     * ຊື່ Primary Key ຂອງຕາຕະລາງ.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * ບອກວ່າ Model ນີ້ມີ timestamps (created_at, updated_at) ຫຼື ບໍ່.
     * ເນື່ອງຈາກຕາຕະລາງ Log ນີ້ມີແຕ່ created_at, ເຮົາຈະປິດ updated_at.
     *
     * @var bool
     */
    public $timestamps = false; // ປິດການຈັດການ updated_at ອັດຕະໂນມັດ

    /**
     * ກຳນົດຊື່ຟີລດ໌ created_at ໃຫ້ Laravel ຮູ້ຈັກ ເມື່ອ $timestamps = false.
     * ອາດຈະບໍ່ຈຳເປັນ ຖ້າຊື່ຟີລດ໌ກົງກັບ default ('created_at').
     */
    const CREATED_AT = 'created_at';

    /**
     * ລາຍຊື່ຟີລດ໌ທີ່ອະນຸຍາດໃຫ້ mass assignable.
     * ໂດຍທົ່ວໄປ Log ອາດຈະບໍ່ mass assign, ແຕ່ກຳນົດໄວ້ກໍ່ໄດ້.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'log_level',
        'log_source',
        'message',
        'context',
        'ip_address',
        'user_id', // Foreign key
        // created_at ໂດຍທົ່ວໄປບໍ່ຄວນ mass assign
    ];

    /**
     * ກຳນົດການແປງປະເພດຂໍ້ມູນ (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'log_level' => LogLevel::class,
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    // ========================================================================
    // Relationships: Belongs To
    // ========================================================================

    /**
     * Relationship: Log ນີ້ອາດຈະກ່ຽວຂ້ອງກັບ User ຄົນດຽວ.
     */
    public function user(): BelongsTo
    {
        // ອ້າງອີງຫາ User Model
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Scope: Filter logs by level.
     */
    public function scopeOfLevel($query, $level)
    {
        return $query->where('log_level', $level);
    }

    /**
     * Scope: Filter logs by source.
     */
    public function scopeFromSource($query, $source)
    {
        return $query->where('log_source', $source);
    }

    /**
     * Scope: Get logs from the last n days.
     */
    public function scopeLastDays($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope: Get only error and critical logs.
     */
    public function scopeOnlyErrors($query)
    {
        return $query->whereIn('log_level', ['error', 'critical']);
    }


    /**
     * ຟັງຊັນ accessor ສຳລັບສະແດງຂໍ້ມູນສະຫຼຸບ (summary)
     */
    public function getSummaryAttribute()
    {
        // ສະຫຼຸບຂໍ້ຄວາມໂດຍຈຳກັດຄວາມຍາວ
        return \Str::limit($this->message, 100);
    }

    /**
     * ຟັງຊັນ accessor ສຳລັບກຳນົດສີຕາມລະດັບ log_level
     */
    public function getLevelColorAttribute()
    {
        return match ($this->log_level) {
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'dark-danger',
            default => 'secondary',
        };
    }

    /**
     * Static helper ສຳລັບສ້າງ log ໄດ້ງ່າຍຂຶ້ນ.
     */
    public static function write($level, $message, $source = null, $context = null, $userId = null)
    {
        return self::create([
            'log_level' => $level,
            'log_source' => $source ?? 'general',
            'message' => $message,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_id' => $userId ?? (auth()->check() ? auth()->id() : null),
        ]);
    }

    /**
     * Static helpers ສຳລັບສ້າງ logs ແຕ່ລະລະດັບ.
     */
    public static function info($message, $source = null, $context = null, $userId = null)
    {
        return self::write('info', $message, $source, $context, $userId);
    }

    public static function warning($message, $source = null, $context = null, $userId = null)
    {
        return self::write('warning', $message, $source, $context, $userId);
    }

    public static function error($message, $source = null, $context = null, $userId = null)
    {
        return self::write('error', $message, $source, $context, $userId);
    }

    public static function critical($message, $source = null, $context = null, $userId = null)
    {
        return self::write('critical', $message, $source, $context, $userId);
    }
}