<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherTask extends Model
{
    use HasFactory;

    /**
     * ຊື່ຕາຕະລາງທີ່ເຊື່ອມຕໍ່ກັບ model ນີ້
     *
     * @var string
     */
    protected $table = 'teacher_tasks';

    /**
     * ຊື່ primary key
     *
     * @var string
     */
    protected $primaryKey = 'task_id';

    /**
     * Attributes ທີ່ສາມາດກຳນົດຄ່າໄດ້ຜ່ານ mass assignment
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'assigned_to',
        'priority',
        'start_date',
        'due_date',
        'status',
        'progress',
        'latest_update',
        'update_history',
        'comments',
        'completion_note',
        'completion_date',
        'rating',
    ];

    /**
     * Attributes ທີ່ຄວນແປງເປັນ native types
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completion_date' => 'datetime',
        'update_history' => 'array',
        'comments' => 'array',
        'progress' => 'integer',
        'rating' => 'integer',
    ];

    /**
     * ຄວາມສຳພັນກັບ model User (ຜູ້ມອບໝາຍວຽກ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * ຄວາມສຳພັນກັບ model Teacher (ຜູ້ຮັບມອບໝາຍວຽກ)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedTo()
    {
        return $this->belongsTo(Teacher::class, 'assigned_to');
    }

    /**
     * ກຳນົດ accessor ສຳລັບການຕັດສິນວ່າວຽກເກີນກຳນົດຫຼືບໍ່
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->due_date < now()->startOfDay() && $this->status !== 'completed';
            },
        );
    }

    /**
     * ອັບເດດສະຖານະຂອງວຽກໂດຍອັດຕະໂນມັດ
     */
    public function updateStatus()
    {
        // ຖ້າສຳເລັດ 100% ແລະ ຍັງບໍ່ມີບັນທຶກວັນທີສຳເລັດ
        if ($this->progress == 100 && $this->completion_date === null) {
            $this->status = 'completed';
            $this->completion_date = now();
        } 
        // ຖ້າວຽກກາຍກຳນົດແລະຍັງບໍ່ສຳເລັດ
        else if ($this->due_date < now()->startOfDay() && $this->status !== 'completed') {
            $this->status = 'overdue';
        } 
        // ຖ້າມີຄວາມຄືບໜ້າແຕ່ຍັງບໍ່ສຳເລັດ
        else if ($this->progress > 0 && $this->progress < 100) {
            $this->status = 'in_progress';
        }
        // ຖ້າຍັງບໍ່ມີຄວາມຄືບໜ້າ
        else if ($this->progress == 0) {
            $this->status = 'pending';
        }

        return $this;
    }

    /**
     * ເພີ່ມບັນທຶກການອັບເດດຄວາມຄືບໜ້າ
     *
     * @param int $progress ຄວາມຄືບໜ້າເປັນເປີເຊັນ
     * @param string $note ບັນທຶກ
     * @param int $updatedBy ຜູ້ອັບເດດ
     * @return $this
     */
    public function addProgressUpdate(int $progress, string $note, int $updatedBy)
    {
        $update = [
            'date' => now()->format('Y-m-d H:i:s'),
            'progress' => $progress,
            'note' => $note,
            'updated_by' => $updatedBy
        ];

        // ອັບເດດ latest_update
        $this->latest_update = $note;
        $this->progress = $progress;

        // ເພີ່ມໃນປະຫວັດການອັບເດດ
        $history = $this->update_history ?? [];
        $history[] = $update;
        $this->update_history = $history;

        // ອັບເດດສະຖານະ
        $this->updateStatus();

        return $this;
    }

    /**
     * ເພີ່ມຄຳເຫັນໃໝ່
     *
     * @param int $userId ລະຫັດຜູ້ໃຊ້
     * @param string $userName ຊື່ຜູ້ໃຊ້
     * @param string $comment ຂໍ້ຄວາມ
     * @return $this
     */
    public function addComment(int $userId, string $userName, string $comment)
    {
        $newComment = [
            'date' => now()->format('Y-m-d H:i:s'),
            'user_id' => $userId,
            'user_name' => $userName,
            'comment' => $comment
        ];

        // ເພີ່ມໃນລາຍການຄຳເຫັນ
        $comments = $this->comments ?? [];
        $comments[] = $newComment;
        $this->comments = $comments;

        return $this;
    }

    /**
     * ຢືນຢັນການສຳເລັດວຽກ
     *
     * @param string $completionNote ບັນທຶກການສຳເລັດວຽກ
     * @return $this
     */
    public function markAsCompleted(string $completionNote)
    {
        $this->status = 'completed';
        $this->progress = 100;
        $this->completion_note = $completionNote;
        $this->completion_date = now();

        return $this;
    }

    /**
     * ກຳນົດຄະແນນປະເມີນວຽກ
     *
     * @param int $rating ຄະແນນ (1-5)
     * @return $this
     */
    public function setRating(int $rating)
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->rating = $rating;
        }

        return $this;
    }
}
