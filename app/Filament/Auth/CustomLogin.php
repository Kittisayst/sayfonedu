<?php

namespace App\Filament\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use App\Models\UserActivity;

class CustomLogin implements LoginResponse
{
    public function toResponse($request)
    {
        $user = Auth::user();
        
        // ກວດສອບວ່າມີຜູ້ໃຊ້ຫຼືບໍ່
        if (!$user) {
            Log::error('CustomLogin - ບໍ່ພົບຜູ້ໃຊ້', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $this->showError('ການເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ. ກະລຸນາລອງໃໝ່.');
            return redirect()->route('filament.admin.auth.login');
        }

        $panel = Filament::getCurrentPanel();
        
        // ກວດສອບສະຖານະ
        if ($user->status !== 'active') {
            Log::warning('CustomLogin - ບັນຊີບໍ່ມີສະຖານະ active', [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'ip' => $request->ip()
            ]);

            // ບັນທຶກກິດຈະກຳການເຂົ້າລະບົບບໍ່ສຳເລັດ
            UserActivity::create([
                'user_id' => $user->user_id,
                'activity_type' => 'login_failed',
                'description' => 'ການເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ: ບັນຊີຖືກປິດໃຊ້ງານ',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'activity_time' => now(),
            ]);
            
            $this->showError('ບັນຊີຂອງທ່ານຖືກປິດໃຊ້ງານ');
            return redirect()->route('filament.admin.auth.login');
        }

        // ກວດສອບສິດໂດຍໃຊ້ Filament's Authorization
        if (!$user->can('access_admin_panel') && !in_array($user->role?->role_name, ['admin', 'school_admin', 'finance_staff'])) {
            Log::warning('CustomLogin - ຜູ້ໃຊ້ບໍ່ມີສິດເຂົ້າໃຊ້', [
                'user_id' => $user->user_id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->role_name ?? 'no_role',
                'ip' => $request->ip()
            ]);

            // ບັນທຶກກິດຈະກຳການເຂົ້າລະບົບບໍ່ສຳເລັດ
            UserActivity::create([
                'user_id' => $user->user_id,
                'activity_type' => 'login_failed',
                'description' => 'ການເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ: ບໍ່ມີສິດເຂົ້າເຖິງ',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'activity_time' => now(),
            ]);
            
            $this->showError('ທ່ານບໍ່ມີສິດໃນການເຂົ້າໃຊ້ລະບົບຄວບຄຸມນີ້');
            return redirect()->route('filament.admin.auth.login');
        }

        // ຖ້າຜ່ານການກວດສອບທຸກຢ່າງ
        Log::info('CustomLogin - ເຂົ້າສູ່ລະບົບສຳເລັດ', [
            'user_id' => $user->user_id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role->role_name,
            'ip' => $request->ip()
        ]);

        // ບັນທຶກເວລາເຂົ້າລະບົບລ່າສຸດ
        $user->last_login = now();
        $user->save();

        // ບັນທຶກກິດຈະກຳການເຂົ້າລະບົບສຳເລັດ
        UserActivity::create([
            'user_id' => $user->user_id,
            'activity_type' => 'login_success',
            'description' => 'ເຂົ້າສູ່ລະບົບສຳເລັດ',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'activity_time' => now(),
        ]);

        return redirect()->intended(Filament::getUrl());
    }

    /**
     * ສະແດງຂໍ້ຄວາມຜິດພາດ
     */
    protected function showError(string $message): void
    {
        Auth::logout();
        
        // ໃຊ້ Filament Notification
        Notification::make()
            ->title('ບໍ່ສາມາດເຂົ້າສູ່ລະບົບ')
            ->body($message)
            ->danger()
            ->send();
    }
}
