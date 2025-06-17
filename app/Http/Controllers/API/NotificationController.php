<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * ດຶງລາຍການແຈ້ງເຕືອນຂອງຜູ້ໃຊ້
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');

        // ກັ່ນຕອງສະເພາະການແຈ້ງເຕືອນທີ່ຍັງບໍ່ໄດ້ອ່ານ (optional)
        if ($request->has('unread_only') && $request->unread_only === 'true') {
            $query->where('is_read', false);
        }

        // ຄົ້ນຫາຕາມປະເພດການແຈ້ງເຕືອນ (optional)
        if ($request->has('type') && !empty($request->type)) {
            $query->where('notification_type', $request->type);
        }

        $notifications = $query->paginate($request->per_page ?? 15);

        return NotificationResource::collection($notifications);
    }

    /**
     * ໝາຍການແຈ້ງເຕືອນເປັນອ່ານແລ້ວ
     */
    public function markAsRead(Notification $notification): Response
    {
        // ກວດສອບວ່າຜູ້ໃຊ້ແມ່ນເຈົ້າຂອງການແຈ້ງເຕືອນບໍ່
        if ($notification->user_id !== auth()->id()) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງການແຈ້ງເຕືອນນີ້'], 403);
        }

        $this->notificationService->markAsRead($notification);

        return response(['message' => 'ໝາຍເປັນອ່ານແລ້ວ']);
    }

    /**
     * ໝາຍການແຈ້ງເຕືອນທັງໝົດເປັນອ່ານແລ້ວ
     */
    public function markAllAsRead(): Response
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response(['message' => 'ໝາຍທັງໝົດເປັນອ່ານແລ້ວ']);
    }

    /**
     * ດຶງຈຳນວນການແຈ້ງເຕືອນທີ່ຍັງບໍ່ໄດ້ອ່ານ
     */
    public function unreadCount(): Response
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response(['count' => $count]);
    }
}