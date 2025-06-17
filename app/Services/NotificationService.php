<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * ສົ່ງການແຈ້ງເຕືອນໃຫ້ຜູ້ໃຊ້
     *
     * @param User|int $user ຜູ້ໃຊ້ຫຼືລະຫັດຜູ້ໃຊ້
     * @param string $title ຫົວຂໍ້ການແຈ້ງເຕືອນ
     * @param string|null $content ເນື້ອໃນການແຈ້ງເຕືອນ
     * @param string $type ປະເພດການແຈ້ງເຕືອນ
     * @param int|null $relatedId ID ທີ່ກ່ຽວຂ້ອງ (ເຊັ່ນ: message_id, grade_id)
     * @return Notification
     */
    public function send($user, string $title, ?string $content = null, string $type = 'other', ?int $relatedId = null): Notification
    {
        $userId = $user instanceof User ? $user->id : $user;

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'notification_type' => $type,
            'related_id' => $relatedId,
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * ສົ່ງການແຈ້ງເຕືອນໃຫ້ຫຼາຍຄົນພ້ອມກັນ
     *
     * @param array $userIds ລາຍການລະຫັດຜູ້ໃຊ້
     * @param string $title ຫົວຂໍ້ການແຈ້ງເຕືອນ
     * @param string|null $content ເນື້ອໃນການແຈ້ງເຕືອນ
     * @param string $type ປະເພດການແຈ້ງເຕືອນ
     * @param int|null $relatedId ID ທີ່ກ່ຽວຂ້ອງ
     * @return array ລາຍການ notifications ທີ່ສ້າງ
     */
    public function sendToMany(array $userIds, string $title, ?string $content = null, string $type = 'other', ?int $relatedId = null): array
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->send($userId, $title, $content, $type, $relatedId);
        }

        return $notifications;
    }

    /**
     * ສົ່ງການແຈ້ງເຕືອນໃຫ້ຄູສອນທັງໝົດ
     *
     * @param string $title ຫົວຂໍ້ການແຈ້ງເຕືອນ
     * @param string|null $content ເນື້ອໃນການແຈ້ງເຕືອນ
     * @param string $type ປະເພດການແຈ້ງເຕືອນ
     * @param int|null $relatedId ID ທີ່ກ່ຽວຂ້ອງ
     * @return array ລາຍການ notifications ທີ່ສ້າງ
     */
    public function notifyAllTeachers(string $title, ?string $content = null, string $type = 'other', ?int $relatedId = null): array
    {
        $teacherUserIds = \App\Models\Teacher::whereNotNull('user_id')->pluck('user_id')->toArray();
        return $this->sendToMany($teacherUserIds, $title, $content, $type, $relatedId);
    }

    /**
     * ສົ່ງການແຈ້ງເຕືອນໃຫ້ນັກຮຽນທັງໝົດ
     *
     * @param string $title ຫົວຂໍ້ການແຈ້ງເຕືອນ
     * @param string|null $content ເນື້ອໃນການແຈ້ງເຕືອນ
     * @param string $type ປະເພດການແຈ້ງເຕືອນ
     * @param int|null $relatedId ID ທີ່ກ່ຽວຂ້ອງ
     * @return array ລາຍການ notifications ທີ່ສ້າງ
     */
    public function notifyAllStudents(string $title, ?string $content = null, string $type = 'other', ?int $relatedId = null): array
    {
        $studentUserIds = \App\Models\Student::whereNotNull('user_id')->pluck('user_id')->toArray();
        return $this->sendToMany($studentUserIds, $title, $content, $type, $relatedId);
    }

    /**
     * ສົ່ງການແຈ້ງເຕືອນໃຫ້ຜູ້ປົກຄອງທັງໝົດ
     *
     * @param string $title ຫົວຂໍ້ການແຈ້ງເຕືອນ
     * @param string|null $content ເນື້ອໃນການແຈ້ງເຕືອນ
     * @param string $type ປະເພດການແຈ້ງເຕືອນ
     * @param int|null $relatedId ID ທີ່ກ່ຽວຂ້ອງ
     * @return array ລາຍການ notifications ທີ່ສ້າງ
     */
    public function notifyAllParents(string $title, ?string $content = null, string $type = 'other', ?int $relatedId = null): array
    {
        $parentUserIds = \App\Models\StudentParent::whereNotNull('user_id')->pluck('user_id')->toArray();
        return $this->sendToMany($parentUserIds, $title, $content, $type, $relatedId);
    }

    /**
     * ໝາຍການແຈ້ງເຕືອນເປັນອ່ານແລ້ວ
     *
     * @param Notification|int $notification ການແຈ້ງເຕືອນຫຼືລະຫັດການແຈ້ງເຕືອນ
     * @return bool
     */
    public function markAsRead($notification): bool
    {
        if (is_numeric($notification)) {
            $notification = Notification::find($notification);
        }

        if (!$notification) {
            return false;
        }

        return $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}