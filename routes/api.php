<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\RequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // ຂໍ້ຄວາມ
    Route::apiResource('messages', MessageController::class);
    Route::patch('messages/{message}/read', [MessageController::class, 'markAsRead']);
    Route::get('messages/unread/count', [MessageController::class, 'unreadCount']);

    // ປະກາດຂ່າວສານ
    Route::get('announcements', [AnnouncementController::class, 'index']);
    Route::get('announcements/{announcement}', [AnnouncementController::class, 'show']);

    // ການແຈ້ງເຕືອນ
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('notifications/unread/count', [NotificationController::class, 'unreadCount']);

    // ຄຳຮ້ອງ
    Route::apiResource('requests', RequestController::class);
});
