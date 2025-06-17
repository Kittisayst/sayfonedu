<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestResource;
use App\Models\Notification;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * ດຶງລາຍການຄຳຮ້ອງຂອງຜູ້ໃຊ້
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = auth()->user();
        $query = null;
        
        // ຖ້າເປັນຜູ້ບໍລິຫານຫຼື manager ສາມາດເຫັນທຸກຄຳຮ້ອງໄດ້
        if ($user->hasAnyRole(['admin', 'manager'])) {
            $query = RequestModel::query();
        } 
        // ຖ້າເປັນຄູສອນ ສາມາດເຫັນຄຳຮ້ອງຂອງຕົນເອງ ແລະ ຂອງນັກຮຽນທີ່ຕົນເປັນອາຈານປະຈຳຫ້ອງ
        elseif ($user->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            
            if ($teacher) {
                // ດຶງຫ້ອງຮຽນທີ່ຄູເປັນອາຈານປະຈຳຫ້ອງ
                $classIds = \App\Models\SchoolClass::where('homeroom_teacher_id', $teacher->id)->pluck('class_id');
                
                // ດຶງລາຍຊື່ນັກຮຽນໃນຫ້ອງທີ່ຄູຮັບຜິດຊອບ
                $studentIds = \App\Models\StudentEnrollment::whereIn('class_id', $classIds)
                    ->pluck('student_id');
                    
                // ດຶງ user_id ຂອງນັກຮຽນດັ່ງກ່າວ
                $studentUserIds = \App\Models\Student::whereIn('student_id', $studentIds)
                    ->whereNotNull('user_id')
                    ->pluck('user_id');
                
                $query = RequestModel::where(function ($q) use ($studentUserIds, $user) {
                    $q->where('user_id', $user->id)
                      ->orWhereIn('user_id', $studentUserIds);
                });
            } else {
                $query = RequestModel::where('user_id', $user->id);
            }
        } 
        // ຜູ້ໃຊ້ອື່ນໆ ເຫັນສະເພາະຄຳຮ້ອງຂອງຕົນເອງ
        else {
            $query = RequestModel::where('user_id', $user->id);
        }
        
        // ກັ່ນຕອງຕາມປະເພດຄຳຮ້ອງ
        if ($request->has('request_type') && !empty($request->request_type)) {
            $query->where('request_type', $request->request_type);
        }
        
        // ກັ່ນຕອງຕາມສະຖານະ
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // ຄົ້ນຫາຕາມຫົວຂໍ້ ຫຼື ເນື້ອໃນ
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
        
        $requests = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);
        
        return RequestResource::collection($requests);
    }

    /**
     * ສ້າງຄຳຮ້ອງໃໝ່
     */
    public function store(Request $request): RequestResource|Response
    {
        $validator = Validator::make($request->all(), [
            'request_type' => 'required|string|in:document_request,leave_request,financial_request,other',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        // ຈັດການກັບໄຟລ໌ແນບ (ຖ້າມີ)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('request-attachments', 'public');
        }

        $newRequest = RequestModel::create([
            'user_id' => auth()->id(),
            'request_type' => $request->request_type,
            'subject' => $request->subject,
            'content' => $request->content,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        // ແຈ້ງເຕືອນຜູ້ບໍລິຫານ ແລະ ຜູ້ກ່ຽວຂ້ອງ
        $this->notifyAdminsAboutNewRequest($newRequest);

        return new RequestResource($newRequest);
    }

    /**
     * ດຶງຂໍ້ມູນຄຳຮ້ອງສະເພາະ
     */
    public function show(RequestModel $request): RequestResource|Response
    {
        $user = auth()->user();
        
        // ກວດສອບສິດການເຂົ້າເຖິງ
        if (!$this->canAccessRequest($user, $request)) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງຄຳຮ້ອງນີ້'], 403);
        }

        return new RequestResource($request);
    }

    /**
     * ອັບເດດຄຳຮ້ອງ
     */
    public function update(Request $httpRequest, RequestModel $request): RequestResource|Response
    {
        $user = auth()->user();
        
        // ກວດສອບສິດການອັບເດດ
        if (!$this->canUpdateRequest($user, $request)) {
            return response(['message' => 'ທ່ານບໍ່ມີສິດອັບເດດຄຳຮ້ອງນີ້'], 403);
        }

        $validator = Validator::make($httpRequest->all(), [
            'subject' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|string|in:pending,processing,approved,rejected',
            'response' => 'sometimes|nullable|string',
            'attachment' => 'sometimes|nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()], 422);
        }

        $data = $httpRequest->only(['subject', 'content', 'status', 'response']);
        
        // ຖ້າມີການປ່ຽນສະຖານະຈາກຄູ່ຮັບຜິດຊອບ
        if ($httpRequest->has('status') && 
            $request->status !== $httpRequest->status && 
            in_array($httpRequest->status, ['approved', 'rejected', 'processing']) &&
            $user->hasAnyRole(['admin', 'manager', 'teacher'])) {
            
            $data['handled_by'] = $user->id;
            $data['handled_at'] = now();
            
            // ແຈ້ງເຕືອນຜູ້ຍື່ນຄຳຮ້ອງ
            $this->notifyRequestStatusChange($request, $httpRequest->status);
        }
        
        // ຈັດການກັບໄຟລ໌ແນບໃໝ່ (ຖ້າມີ)
        if ($httpRequest->hasFile('attachment')) {
            // ລຶບໄຟລ໌ເກົ່າ (ຖ້າມີ)
            if ($request->attachment) {
                Storage::disk('public')->delete($request->attachment);
            }
            
            $data['attachment'] = $httpRequest->file('attachment')->store('request-attachments', 'public');
        }
        
        $request->update($data);

        return new RequestResource($request);
    }

    /**
     * ລຶບຄຳຮ້ອງ
     */
    public function destroy(RequestModel $request): Response
    {
        $user = auth()->user();
        
        // ຜູ້ໃຊ້ສາມາດລຶບຄຳຮ້ອງຂອງຕົນເອງໄດ້ສະເພາະຄຳຮ້ອງທີ່ຍັງລໍຖ້າການດຳເນີນການ
        if ($request->user_id === $user->id && $request->status === 'pending') {
            // ລຶບໄຟລ໌ແນບ (ຖ້າມີ)
            if ($request->attachment) {
                Storage::disk('public')->delete($request->attachment);
            }
            
            $request->delete();
            
            return response(['message' => 'ລຶບຄຳຮ້ອງສຳເລັດ']);
        }
        
        // ຜູ້ບໍລິຫານສາມາດລຶບຄຳຮ້ອງໄດ້ທຸກສະຖານະ
        if ($user->hasAnyRole(['admin', 'manager'])) {
            // ລຶບໄຟລ໌ແນບ (ຖ້າມີ)
            if ($request->attachment) {
                Storage::disk('public')->delete($request->attachment);
            }
            
            $request->delete();
            
            return response(['message' => 'ລຶບຄຳຮ້ອງສຳເລັດ']);
        }
        
        return response(['message' => 'ທ່ານບໍ່ມີສິດລຶບຄຳຮ້ອງນີ້'], 403);
    }
    
    /**
     * ກວດສອບສິດການເຂົ້າເຖິງຄຳຮ້ອງ
     */
    private function canAccessRequest($user, $request): bool
    {
        // ຜູ້ບໍລິຫານເຂົ້າເຖິງໄດ້ທຸກຄຳຮ້ອງ
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }
        
        // ເຈົ້າຂອງຄຳຮ້ອງເຂົ້າເຖິງໄດ້
        if ($request->user_id === $user->id) {
            return true;
        }
        
        // ຄູສອນສາມາດເຂົ້າເຖິງຄຳຮ້ອງຂອງນັກຮຽນທີ່ຕົນຮັບຜິດຊອບໄດ້
        if ($user->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            
            if ($teacher) {
                // ດຶງຫ້ອງຮຽນທີ່ຄູເປັນອາຈານປະຈຳຫ້ອງ
                $classIds = \App\Models\SchoolClass::where('homeroom_teacher_id', $teacher->id)->pluck('class_id');
                
                // ດຶງລາຍຊື່ນັກຮຽນໃນຫ້ອງທີ່ຄູຮັບຜິດຊອບ
                $studentIds = \App\Models\StudentEnrollment::whereIn('class_id', $classIds)
                    ->pluck('student_id');
                    
                // ດຶງ user_id ຂອງນັກຮຽນດັ່ງກ່າວ
                $studentUserIds = \App\Models\Student::whereIn('student_id', $studentIds)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                
                if (in_array($request->user_id, $studentUserIds)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * ກວດສອບສິດການອັບເດດຄຳຮ້ອງ
     */
    private function canUpdateRequest($user, $request): bool
    {
        // ຜູ້ບໍລິຫານອັບເດດໄດ້ທຸກຄຳຮ້ອງ
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }
        
        // ເຈົ້າຂອງຄຳຮ້ອງອັບເດດໄດ້ສະເພາະຄຳຮ້ອງທີ່ຍັງລໍຖ້າການດຳເນີນການ
        if ($request->user_id === $user->id && $request->status === 'pending') {
            return true;
        }
        
        // ຄູສອນສາມາດອັບເດດຄຳຮ້ອງຂອງນັກຮຽນທີ່ຕົນຮັບຜິດຊອບໄດ້
        if ($user->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            
            if ($teacher) {
                // ດຶງຫ້ອງຮຽນທີ່ຄູເປັນອາຈານປະຈຳຫ້ອງ
                $classIds = \App\Models\SchoolClass::where('homeroom_teacher_id', $teacher->id)->pluck('class_id');
                
                // ດຶງລາຍຊື່ນັກຮຽນໃນຫ້ອງທີ່ຄູຮັບຜິດຊອບ
                $studentIds = \App\Models\StudentEnrollment::whereIn('class_id', $classIds)
                    ->pluck('student_id');
                    
                // ດຶງ user_id ຂອງນັກຮຽນດັ່ງກ່າວ
                $studentUserIds = \App\Models\Student::whereIn('student_id', $studentIds)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                
                if (in_array($request->user_id, $studentUserIds)) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * ແຈ້ງເຕືອນຜູ້ບໍລິຫານກ່ຽວກັບຄຳຮ້ອງໃໝ່
     */
    private function notifyAdminsAboutNewRequest(RequestModel $request): void
    {
        // ດຶງລາຍຊື່ຜູ້ບໍລິຫານທັງໝົດ
        $adminUserIds = \App\Models\User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'manager']);
        })->pluck('id')->toArray();
        
        foreach ($adminUserIds as $adminId) {
            Notification::create([
                'user_id' => $adminId,
                'title' => 'ມີຄຳຮ້ອງໃໝ່',
                'content' => "ມີຄຳຮ້ອງໃໝ່: {$request->subject}",
                'notification_type' => 'request_update',
                'related_id' => $request->id,
            ]);
        }
        
        // ຖ້າເປັນຄຳຮ້ອງຈາກນັກຮຽນ ແຈ້ງເຕືອນຄູປະຈຳຫ້ອງດ້ວຍ
        $student = \App\Models\Student::where('user_id', $request->user_id)->first();
        
        if ($student) {
            // ດຶງ StudentEnrollment ທີ່ active ຂອງນັກຮຽນ
            $enrollment = \App\Models\StudentEnrollment::where('student_id', $student->student_id)
                ->where('enrollment_status', 'enrolled')
                ->first();
                
                if ($enrollment) {
                    // ດຶງຂໍ້ມູນຫ້ອງຮຽນ
                    $class = \App\Models\SchoolClass::find($enrollment->class_id);
                    
                    if ($class && $class->homeroom_teacher_id) {
                        // ດຶງຂໍ້ມູນຄູປະຈຳຫ້ອງ
                        $teacher = \App\Models\Teacher::find($class->homeroom_teacher_id);
                        
                        if ($teacher && $teacher->user_id) {
                            Notification::create([
                                'user_id' => $teacher->user_id,
                                'title' => 'ມີຄຳຮ້ອງໃໝ່ຈາກນັກຮຽນ',
                                'content' => "ມີຄຳຮ້ອງໃໝ່ຈາກນັກຮຽນ {$student->first_name_lao} {$student->last_name_lao}: {$request->subject}",
                                'notification_type' => 'request_update',
                                'related_id' => $request->id,
                            ]);
                        }
                    }
                }
            }
        }
        
        /**
         * ແຈ້ງເຕືອນຜູ້ຍື່ນຄຳຮ້ອງກ່ຽວກັບການປ່ຽນແປງສະຖານະ
         */
        private function notifyRequestStatusChange(RequestModel $request, string $newStatus): void
        {
            $statusText = match($newStatus) {
                'processing' => 'ກຳລັງດຳເນີນການ',
                'approved' => 'ອະນຸມັດແລ້ວ',
                'rejected' => 'ປະຕິເສດ',
                default => 'ປ່ຽນສະຖານະເປັນ ' . $newStatus,
            };
            
            Notification::create([
                'user_id' => $request->user_id,
                'title' => "ຄຳຮ້ອງຂອງທ່ານ{$statusText}",
                'content' => "ຄຳຮ້ອງ '{$request->subject}' ຂອງທ່ານໄດ້{$statusText}",
                'notification_type' => 'request_update',
                'related_id' => $request->id,
            ]);
        }
    }