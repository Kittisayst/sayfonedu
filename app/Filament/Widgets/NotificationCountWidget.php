<?php

namespace App\Filament\Widgets;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Request;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotificationCountWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        // ຈຳນວນຂໍ້ຄວາມທີ່ຍັງບໍ່ໄດ້ອ່ານ
        $unreadMessagesCount = Message::where('receiver_id', $userId)
            ->where('read_status', false)
            ->count();

        // ຈຳນວນການແຈ້ງເຕືອນທີ່ຍັງບໍ່ໄດ້ອ່ານ
        $unreadNotificationsCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        // ຈຳນວນຄຳຮ້ອງທີ່ລໍຖ້າການດຳເນີນການ
        $pendingRequestsCount = 0;
        
        // ຖ້າຜູ້ໃຊ້ເປັນຜູ້ບໍລິຫານ/ຜູ້ຈັດການ ໃຫ້ສະແດງຈຳນວນຄຳຮ້ອງທີ່ຍັງບໍ່ໄດ້ດຳເນີນການທັງໝົດ
        if (auth()->user()->hasAnyRole(['admin', 'manager'])) {
            $pendingRequestsCount = Request::where('status', 'pending')->count();
        } 
        // ຖ້າເປັນຄູສອນ ໃຫ້ສະແດງຈຳນວນຄຳຮ້ອງຂອງນັກຮຽນທີ່ຮັບຜິດຊອບ
        elseif (auth()->user()->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $userId)->first();
            
            if ($teacher) {
                $classIds = \App\Models\SchoolClass::where('homeroom_teacher_id', $teacher->id)->pluck('class_id');
                $studentIds = \App\Models\StudentEnrollment::whereIn('class_id', $classIds)->pluck('student_id');
                $studentUserIds = \App\Models\Student::whereIn('student_id', $studentIds)
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                
                $pendingRequestsCount = Request::whereIn('user_id', $studentUserIds)
                    ->where('status', 'pending')
                    ->count();
            }
        }

        return [
            Stat::make('ຂໍ້ຄວາມທີ່ຍັງບໍ່ໄດ້ອ່ານ', $unreadMessagesCount)
                ->icon('heroicon-o-envelope')
                ->color($unreadMessagesCount > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.messages.index', ['tableFilters[read_status][value]' => '0'])),

            Stat::make('ການແຈ້ງເຕືອນທີ່ຍັງບໍ່ໄດ້ອ່ານ', $unreadNotificationsCount)
                ->icon('heroicon-o-bell')
                ->color($unreadNotificationsCount > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.notifications.index', ['tableFilters[is_read][value]' => '0'])),

            Stat::make('ຄຳຮ້ອງທີ່ລໍຖ້າ', $pendingRequestsCount)
                ->icon('heroicon-o-document-text')
                ->color($pendingRequestsCount > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.requests.index', ['tableFilters[status][value]' => 'pending'])),
        ];
    }
}