<?php

namespace App\Exceptions;

use App\Facades\SysLog;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // ບັນທຶກ exceptions ລົງໃນ SystemLog
            if ($this->shouldReport($e)) {
                SysLog::error('Exception: ' . $e->getMessage(), get_class($e), [
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'previous' => $e->getPrevious() ? get_class($e->getPrevious()) : null,
                ]);
            }
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 401);
            }

            try {
                $panel = Filament::getCurrentPanel();
                if ($panel) {
                    $panelId = $panel->getId();
                    $loginRouteName = "filament.{$panelId}.auth.login";

                    if (Route::has($loginRouteName)) {
                        return redirect()->guest(route($loginRouteName))
                            ->withErrors(['login' => $e->getMessage()]);
                    }
                }
            } catch (\Exception $exception) {
                // Handle cases where panel context might not be available as expected
                // Or fallback to default behavior
            }

            return redirect()->guest($e->redirectTo() ?? route('login'));
        });
    }
}
