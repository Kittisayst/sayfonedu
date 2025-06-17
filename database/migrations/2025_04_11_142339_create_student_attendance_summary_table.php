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
        Schema::create('student_attendance_summary', function (Blueprint $table) {
            $table->id('summary_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນ (FK)'); // FK to classes, NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->integer('month')->comment('ເດືອນທີ່ສະຫຼຸບ (1-12)'); // NOT NULL
            $table->integer('year')->comment('ປີ ຄ.ສ. ທີ່ສະຫຼຸບ'); // NOT NULL
            $table->integer('total_days')->default(0)->comment('ຈຳນວນວັນຮຽນທັງໝົດໃນເດືອນ'); // NOT NULL, default 0
            $table->integer('present_days')->default(0)->comment('ຈຳນວນວັນທີ່ມາຮຽນ'); // NOT NULL, default 0
            $table->integer('absent_days')->default(0)->comment('ຈຳນວນວັນທີ່ຂາດຮຽນ'); // NOT NULL, default 0
            $table->integer('late_days')->default(0)->comment('ຈຳນວນວັນທີ່ມາຊ້າ'); // NOT NULL, default 0
            $table->integer('excused_days')->default(0)->comment('ຈຳນວນວັນທີ່ລາພັກ'); // NOT NULL, default 0
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('cascade'); // Assume summary is tied to the class context

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict');

            // Define Unique Constraint
            $table->unique(['student_id', 'academic_year_id', 'year', 'month'], 'UQ_StudAttSumm_student_period');
                // ->comment('One summary record per student per month per academic year');

            // Define Indexes
            $table->index('student_id', 'IDX_StudAttSumm_student');
            $table->index('class_id', 'IDX_StudAttSumm_class');
            $table->index('academic_year_id', 'IDX_StudAttSumm_acad_year');
            $table->index(['academic_year_id', 'year', 'month'], 'IDX_StudAttSumm_period'); // Index for querying by period

            // Note: This table is typically populated by a scheduled process summarizing data
            // from the 'attendance' (D34) table for reporting efficiency.
            // Note: Application logic/process should validate day counts if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_attendance_summary');
    }
};
