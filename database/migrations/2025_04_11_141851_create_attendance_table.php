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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('attendance_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນ (FK)'); // FK to classes, NOT NULL
            $table->unsignedBigInteger('subject_id')->nullable()->comment('ລະຫັດວິຊາ (FK)'); // FK to subjects, Nullable for daily attendance
            $table->date('attendance_date')->comment('ວັນທີທີ່ບັນທຶກ'); // NOT NULL
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->comment('ສະຖານະ: present, absent, late, excused'); // NOT NULL
            $table->text('reason')->nullable()->comment('ເຫດຜົນການຂາດ/ລາ/ມາຊ້າ');
            $table->unsignedBigInteger('recorded_by')->nullable()->comment('ລະຫັດຜູ້ບັນທຶກ (FK)'); // FK to users, Nullable
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('cascade');

            $table->foreign('subject_id')
                  ->references('subject_id')->on('subjects')
                  ->onDelete('set null');

            $table->foreign('recorded_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null');

            // Define Unique Constraint
            $table->unique(['student_id', 'attendance_date', 'subject_id'], 'UQ_Attendance_stud_date_subj');
                // ->comment('Prevent duplicate entry for the same student, date, and subject (if specified)');

            // Define Indexes
            $table->index('student_id', 'IDX_Attendance_student');
            $table->index('class_id', 'IDX_Attendance_class');
            $table->index('subject_id', 'IDX_Attendance_subject');
            $table->index('recorded_by', 'IDX_Attendance_recorder');
            $table->index('attendance_date', 'IDX_Attendance_date');
            $table->index('status', 'IDX_Attendance_status');

            // Note: The unique constraint behavior with NULL subject_id depends on the database.
            // MySQL/MariaDB typically allow multiple NULLs in unique constraints.
            // Application logic might be needed to enforce only one record with NULL subject_id per student/date if required.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
