<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id('grade_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນ (FK)'); // FK to classes, NOT NULL
            $table->unsignedBigInteger('subject_id')->comment('ລະຫັດວິຊາ (FK)'); // FK to subjects, NOT NULL
            $table->unsignedBigInteger('exam_id')->comment('ລະຫັດການສອບເສັງ (FK)'); // FK to examinations, NOT NULL
            $table->decimal('marks', 5, 2)->comment('ຄະແນນທີ່ໄດ້ຮັບ'); // NOT NULL
            $table->string('grade_letter', 5)->nullable()->comment('ຄະແນນຕົວອັກສອນ (ເກຣດ)');
            $table->text('comments')->nullable()->comment('ໝາຍເຫດ/ຄຳຄິດເຫັນຈາກຄູ');
            $table->boolean('is_published')->default(false)->comment('ສະຖານະເຜີຍແຜ່ (TRUE=ເຜີຍແຜ່ແລ້ວ)'); // NOT NULL, default false
            $table->unsignedBigInteger('graded_by')->nullable()->comment('ລະຫັດຜູ້ໃຫ້ຄະແນນ (FK)'); // FK to users, Nullable
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('restrict');

            $table->foreign('subject_id')
                  ->references('subject_id')->on('subjects')
                  ->onDelete('restrict');

            $table->foreign('exam_id')
                  ->references('exam_id')->on('examinations')
                  ->onDelete('restrict');

            $table->foreign('graded_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null');

            // Define Unique Constraint
            $table->unique(['student_id', 'exam_id', 'subject_id'], 'UQ_Grades_student_exam_subject');
                // ->comment('Prevent duplicate grade entry for same student, exam, subject');

            // Define Indexes
            $table->index('student_id', 'IDX_Grades_student');
            $table->index('class_id', 'IDX_Grades_class');
            $table->index('subject_id', 'IDX_Grades_subject');
            $table->index('exam_id', 'IDX_Grades_exam');
            $table->index('graded_by', 'IDX_Grades_grader');
            $table->index('is_published', 'IDX_Grades_published');

            // Note: Application logic should validate marks range (>= 0 and <= Examinations.total_marks).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
