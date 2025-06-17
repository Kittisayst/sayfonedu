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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id('exam_id'); // PK
            $table->string('exam_name', 255)->comment('ຊື່ການສອບເສັງ (ຕົວຢ່າງ: ເສັງພາກຮຽນ 1)'); // NOT NULL
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'assignment'])->comment('ປະເພດ: midterm, final, quiz, assignment'); // NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->date('start_date')->nullable()->comment('ວັນທີເລີ່ມໄລຍະເວລາສອບເສັງ');
            $table->date('end_date')->nullable()->comment('ວັນທີສິ້ນສຸດໄລຍະເວລາສອບເສັງ');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍເພີ່ມເຕີມ');
            $table->integer('total_marks')->comment('ຄະແນນເຕັມສຳລັບການສອບເສັງນີ້'); // NOT NULL
            $table->integer('passing_marks')->nullable()->comment('ຄະແນນຂັ້ນຕ່ຳເພື່ອຖືວ່າຜ່ານ');
            $table->enum('status', ['upcoming', 'ongoing', 'completed'])->default('upcoming')->comment('ສະຖານະ: upcoming, ongoing, completed'); // NOT NULL, default upcoming
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict'); // Prevent deleting academic year if exams exist

            // Define Unique Constraint
            $table->unique(['academic_year_id', 'exam_name'], 'UQ_Exams_name_year');
                // ->comment('Exam name must be unique within an academic year');

            // Define Indexes
            $table->index('academic_year_id', 'IDX_Exams_acad_year');
            $table->index('exam_type', 'IDX_Exams_type');
            $table->index('status', 'IDX_Exams_status');
            $table->index(['start_date', 'end_date'], 'IDX_Exams_dates');

            // Note: Application logic should validate date sequence (end_date >= start_date),
            // total_marks > 0, and passing_marks relation to total_marks if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
