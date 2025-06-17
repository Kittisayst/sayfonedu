<?php

namespace App\Observers;

use App\Models\StudentSibling;

class StudentSiblingObserver
{
    // ຮອງຮັບກໍລະນີລຶບຂໍ້ມູນ
    public function deleted(StudentSibling $studentSibling): void
    {
        // ລຶບຄວາມສຳພັນກົງກັນຂ້າມນຳ
        StudentSibling::where('student_id', $studentSibling->sibling_student_id)
            ->where('sibling_student_id', $studentSibling->student_id)
            ->delete();
    }
}
