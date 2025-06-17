<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Auth\AuthenticationException;

class CheckPanelAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // ກວດສອບສະເພາະເມື່ອມີ Panel context ແລະ User login ຢູ່
        if (! Filament::getCurrentPanel() || ! Auth::check()) {
            return $next($request);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $panel = Filament::getCurrentPanel();
        $panelId = $panel->getId();
        $loginRouteName = "filament.{$panelId}.auth.login"; // ປະກອບຊື່ route ໂດຍໃຊ້ panel ID

        $errorMessage = null;

        // ກວດສອບ Status
        if ($user->status !== 'active') {
            $status = $user->status;
            $errorMessage = __('ບັນຊີຂອງທ່ານຖືກປິດໃຊ້ງານ') . ($status ? " (ສະຖານະ: {$status})" : '');
        }
        // ກວດສອບ Role (ຖ້າ status OK)
        elseif (! $user->role) {
            $errorMessage = __('ທ່ານບໍ່ມີບົດບາດໃນລະບົບ');
        }
        // ກວດສອບ Permission (ຖ້າ status ແລະ role OK)
        elseif (! $user->canAccessPanel($panel)) { // ໃຊ້ canAccessPanel ທີ່ return boolean
            $roleName = $user->role->role_name;
            $errorMessage = __('ທ່ານບໍ່ມີສິດເຂົ້າໃຊ້ລະບົບນີ້ (ບົດບາດ: :role)', ['role' => $roleName]);
        }

        // ຖ້າກວດພົບຂໍ້ຜິດພາດ, ໃຫ້ logout, invalidate session, ແລ້ວ redirect ກັບໄປໜ້າ Login ພ້ອມ flash message ທຳມະດາ
        if ($errorMessage !== null) {
            Auth::logout(); // Log out user
            $request->session()->invalidate(); // Invalidate session
            $request->session()->regenerateToken(); // Regenerate token

            // Redirect ກັບໄປໜ້າ Login ພ້ອມ flash message ທຳມະດາ
            return Redirect::route($loginRouteName)
                   ->with('login_error', $errorMessage); // <--- ໃຊ້ key 'login_error'
        }

        // ຖ້າທຸກຢ່າງຖືກຕ້ອງ, ດຳເນີນ request ຕໍ່ໄປ
        return $next($request);
    }
} 