<?php

namespace App\Traits;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        // ຕິດຕາມການສ້າງ
        static::created(function ($model) {
            static::logChange('create', $model);
        });
        
        // ຕິດຕາມການອັບເດດ
        static::updated(function ($model) {
            static::logChange('update', $model);
        });
        
        // ຕິດຕາມການລຶບ
        static::deleted(function ($model) {
            static::logChange('delete', $model);
        });
    }
    
    protected static function logChange($action, $model)
    {
        $modelName = class_basename($model);
        $userId = Auth::id() ?? null;
        $modelId = $model->getKey();
        
        // ສ້າງລາຍລະອຽດສຳລັບການບັນທຶກ
        $description = "{$action} {$modelName} ID: {$modelId}";
        
        // ຖ້າເປັນການອັບເດດ, ເກັບການປ່ຽນແປງ
        if ($action === 'update') {
            $changes = $model->getChanges();
            unset($changes['updated_at']); // ບໍ່ຕ້ອງບັນທຶກການປ່ຽນແປງໃນ updated_at
            
            if (count($changes) > 0) {
                $changedFields = implode(', ', array_keys($changes));
                $description .= " (changed fields: {$changedFields})";
            }
        }
        
        // ບັນທຶກກິດຈະກຳ
        UserActivity::create([
            'user_id' => $userId,
            'activity_type' => "{$action}_{$modelName}",
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'activity_time' => now(),
        ]);
    }
}