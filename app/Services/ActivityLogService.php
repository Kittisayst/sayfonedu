<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * ບັນທຶກກິດຈະກຳຜູ້ໃຊ້
     *
     * @param string $activityType ປະເພດກິດຈະກຳ
     * @param string|null $description ລາຍລະອຽດເພີ່ມເຕີມ
     * @param int|null $userId ລະຫັດຜູ້ໃຊ້ (ຖ້າບໍ່ລະບຸຈະໃຊ້ຜູ້ໃຊ້ປັດຈຸບັນ)
     * @return UserActivity
     */
    public function log(string $activityType, ?string $description = null, ?int $userId = null): UserActivity
    {
        // ຖ້າບໍ່ລະບຸ user_id ຈະໃຊ້ຜູ້ໃຊ້ປັດຈຸບັນ
        $userId = $userId ?? Auth::id();
        
        return UserActivity::create([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'activity_time' => now(),
        ]);
    }
}