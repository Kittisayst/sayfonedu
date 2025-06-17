<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user || !$user->role || !$user->role->permissions()->where('permission_name', $permission)->exists()) {
            // ຖ້າເປັນ API request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ທ່ານບໍ່ມີສິດໃນການເຂົ້າເຖິງຂໍ້ມູນນີ້',
                ], 403);
            }

            // ຖ້າເປັນ web request
            abort(403, 'ທ່ານບໍ່ມີສິດໃນການເຂົ້າເຖິງຂໍ້ມູນນີ້');
        }

        return $next($request);
    }
}
