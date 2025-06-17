<?php

namespace App\Http\Middleware;

use App\Facades\SysLog;
use Closure;
use Illuminate\Http\Request;

class LogImportantRequests
{
    protected $importantRoutes = [
        'login', 'register', 'password/*', 'admin/*', 'api/*'
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // ກວດສອບວ່າ route ນີ້ຢູ່ໃນລາຍການທີ່ຕ້ອງການບັນທຶກຫຼືບໍ່
        if ($this->shouldLogRoute($request->route()->uri())) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    protected function shouldLogRoute($uri)
    {
        foreach ($this->importantRoutes as $route) {
            if (fnmatch($route, $uri)) {
                return true;
            }
        }
        return false;
    }

    protected function logRequest(Request $request, $response)
    {
        $statusCode = $response->getStatusCode();
        $level = $statusCode >= 400 ? 'error' : 'info';
        
        SysLog::log(
            $level,
            "Request: {$request->method()} {$request->fullUrl()} ({$statusCode})",
            'HttpRequest',
            [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'input' => $request->except(['password', 'password_confirmation']),
                'status_code' => $statusCode,
                'user_agent' => $request->userAgent(),
            ]
        );
    }
}