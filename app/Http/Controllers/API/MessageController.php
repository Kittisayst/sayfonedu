<?php

namespace App\Http\Controllers\API;

use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /**
     * ດຶງລາຍການຂໍ້ຄວາມຂອງຜູ້ໃຊ້
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Message::where(function ($query) {
            $query->where('sender_id', auth()->id())
                ->orWhere('receiver_id', auth()->id());
        })
            ->orderBy('created_at', 'desc');

        // ກັ່ນຕອງສະເພາະຂໍ້ຄວາມທີ່ຍັງບໍ່ໄດ້ອ່ານ (optional)
        if ($request->has('unread_only') && $request->unread_only === 'true') {
            $query->where('receiver_id', auth()->id())
                ->where('read_status', false);
        }

        // ກັ່ນຕອງສະເພາະຂໍ້ຄວາມກັບຜູ້ໃຊ້ສະເພາະ (optional)
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where(function ($query) use ($request) {
                $query->where('sender_id', $request->user_id)
                    ->where('receiver_id', auth()->id());
            })->orWhere(function ($query) use ($request) {
                $query->where('sender_id', auth()->id())
                    ->where('receiver_id', $request->user_id);
            });
        }

        // ຄົ້ນຫາຕາມຫົວຂໍ້ ຫຼື ເນື້ອໃນຂໍ້ຄວາມ (optional)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('subject', 'like', "%{$search}%")
                    ->orWhere('message_content', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate($request->per_page ?? 15);

        return MessageResource::collection($messages);
    }

    /**
     * ສ້າງຂໍ້ຄວາມໃໝ່
     */
    public function store(Request $request): MessageResource|Response
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'message_content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        // ຈັດການກັບໄຟລ໌ແນບ (ຖ້າມີ)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('message-attachments', 'public');
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'message_content' => $request->message_content,
            'attachment' => $attachmentPath,
            'read_status' => false,
        ]);

        // ແຈ້ງເຕືອນຜູ້ຮັບ
        event(new NewMessageEvent($message));

        return new MessageResource($message);
    }

    /**
     * ດຶງຂໍ້ມູນຂໍ້ຄວາມສະເພາະ
     */
    public function show(Message $message): MessageResource|Response
    {
        // ກວດສອບສິດການເຂົ້າເຖິງ
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງຂໍ້ຄວາມນີ້'], 403);
        }

        // ຖ້າຜູ້ໃຊ້ປັດຈຸບັນເປັນຜູ້ຮັບຂໍ້ຄວາມ ແລະ ຍັງບໍ່ໄດ້ອ່ານ
        if ($message->receiver_id === auth()->id() && !$message->read_status) {
            $message->update([
                'read_status' => true,
                'read_at' => now(),
            ]);
        }

        return new MessageResource($message);
    }

    /**
     * ໝາຍຂໍ້ຄວາມເປັນອ່ານແລ້ວ
     */
    public function markAsRead(Message $message): Response
    {
        // ກວດສອບວ່າຜູ້ໃຊ້ແມ່ນຜູ້ຮັບຂໍ້ຄວາມບໍ່
        if ($message->receiver_id !== auth()->id()) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດໝາຍຂໍ້ຄວາມນີ້ເປັນອ່ານແລ້ວ'], 403);
        }

        $message->update([
            'read_status' => true,
            'read_at' => now(),
        ]);

        return response(['message' => 'ໝາຍເປັນອ່ານແລ້ວ']);
    }

    /**
     * ລຶບຂໍ້ຄວາມ
     */
    public function destroy(Message $message): Response
    {
        // ກວດສອບສິດການເຂົ້າເຖິງ (ທຳລາຍໄດ້ສະເພາະຜູ້ສົ່ງ)
        if ($message->sender_id !== auth()->id()) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດລຶບຂໍ້ຄວາມນີ້'], 403);
        }

        $message->delete();

        return response(['message' => 'ລຶບຂໍ້ຄວາມສຳເລັດ']);
    }

    /**
     * ດຶງຈຳນວນຂໍ້ຄວາມທີ່ຍັງບໍ່ໄດ້ອ່ານ
     */
    public function unreadCount(): Response
    {
        $count = Message::where('receiver_id', auth()->id())
            ->where('read_status', false)
            ->count();

        return response(['count' => $count]);
    }
}