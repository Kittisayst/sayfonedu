<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    protected $activityLogService;
    
    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }
    
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ກວດວ່າຜູ້ໃຊ້ເຂົ້າສູ່ລະບົບແລ້ວຫຼືບໍ່
        if (auth()->check()) {
            // ປະມວນຜົນ request ກ່ອນ
            $response = $next($request);
            
            // ບັນທຶກກິດຈະກຳຫຼັງຈາກ request ສຳເລັດ
            $this->logActivity($request, $response);
            
            return $response;
        }
        
        return $next($request);
    }
    
    /**
     * ບັນທຶກກິດຈະກຳອີງຕາມຂໍ້ມູນ request/response
     */
    protected function logActivity(Request $request, Response $response): void
    {
        // ເກັບຂໍ້ມູນເສັ້ນທາງແລະວິທີການ
        $path = $request->path();
        $method = $request->method();
        
        // ເກັບລາຍລະອຽດຂອງກິດຈະກຳ
        $description = "Route: $method $path";
        
        // ປະເພດກິດຈະກຳຂຶ້ນກັບ request
        $activityType = $this->determineActivityType($request, $response);
        
        // ບັນທຶກກິດຈະກຳ
        $this->activityLogService->log($activityType, $description);
    }
    
    /**
     * ກຳນົດປະເພດກິດຈະກຳຈາກ request
     */
    protected function determineActivityType(Request $request, Response $response): string
    {
        $method = $request->method();
        $path = $request->path();
        
        // ກຳນົດປະເພດກິດຈະກຳຕາມເສັ້ນທາງແລະວິທີການ
        if (strpos($path, 'login') !== false && $method === 'POST') {
            return 'login';
        }
        
        if (strpos($path, 'logout') !== false) {
            return 'logout';
        }
        
        // ກຳນົດປະເພດຕາມ HTTP method
        switch ($method) {
            case 'POST':
                return 'create';
            case 'PUT':
            case 'PATCH':
                return 'update';
            case 'DELETE':
                return 'delete';
            case 'GET':
            default:
                return 'view';
        }
    }
}