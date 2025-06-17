<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnnouncementController extends Controller
{
    /**
     * ດຶງລາຍການປະກາດຂ່າວສານ
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = auth()->user();
        $userType = $this->getUserType($user);
        
        $query = Announcement::query()
            ->where(function($query) use ($userType) {
                $query->where('target_group', 'all')
                    ->orWhere('target_group', $userType);
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where(function($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now()->toDateString());
            });
        
        // ຄົ້ນຫາຕາມຫົວຂໍ້ ຫຼື ເນື້ອໃນ
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        // ຈັດລຽງໂດຍໃຫ້ Pinned ຢູ່ເທິງສຸດກ່ອນ, ຈາກນັ້ນຈັດລຽງຕາມວັນທີສ້າງ
        $announcements = $query->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);
        
        return AnnouncementResource::collection($announcements);
    }

    /**
     * ດຶງຂໍ້ມູນປະກາດຂ່າວສານສະເພາະ
     */
    public function show(Announcement $announcement): AnnouncementResource
    {
        $user = auth()->user();
        $userType = $this->getUserType($user);
        
        // ກວດສອບວ່າຜູ້ໃຊ້ປັດຈຸບັນສາມາດເຂົ້າເຖິງປະກາດນີ້ໄດ້ບໍ່
        if ($announcement->target_group !== 'all' && $announcement->target_group !== $userType) {
            abort(403, 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງປະກາດນີ້');
        }
        
        return new AnnouncementResource($announcement);
    }
    
    /**
     * ກຳນົດປະເພດຂອງຜູ້ໃຊ້ (teachers, students, parents)
     */
    private function getUserType($user): string
    {
        if ($user->hasRole('teacher')) {
            return 'teachers';
        } elseif ($user->hasRole('student')) {
            return 'students';
        } elseif ($user->hasRole('parent')) {
            return 'parents';
        }
        
        return 'all'; // default
    }
}