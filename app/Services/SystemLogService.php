<?php

namespace App\Services;

use App\Models\SystemLog;
use Throwable;

class SystemLogService
{
    /**
     * ບັນທຶກຂໍ້ຄວາມລົງໃນລະບົບ Logging
     */
    public function log($level, $message, $source = null, array $context = null)
    {
        try {
            return SystemLog::create([
                'log_level' => $level,
                'log_source' => $source,
                'message' => $message,
                'context' => $context ? json_encode($context) : null,
                'ip_address' => request()->ip(),
                'user_id' => auth()->id(),
            ]);
        } catch (Throwable $e) {
            // ໃຊ້ Laravel's logging system ເມື່ອບໍ່ສາມາດບັນທຶກລົງຖານຂໍ້ມູນໄດ້
            logger()->error("ບໍ່ສາມາດບັນທຶກ system log ໄດ້: " . $e->getMessage(), [
                'original_message' => $message,
                'original_context' => $context,
            ]);

            return false;
        }
    }

    // Helper methods for different log levels
    public function info($message, $source = null, array $context = null)
    {
        return $this->log('info', $message, $source, $context);
    }

    public function warning($message, $source = null, array $context = null)
    {
        return $this->log('warning', $message, $source, $context);
    }

    public function error($message, $source = null, array $context = null)
    {
        return $this->log('error', $message, $source, $context);
    }

    public function critical($message, $source = null, array $context = null)
    {
        return $this->log('critical', $message, $source, $context);
    }
}