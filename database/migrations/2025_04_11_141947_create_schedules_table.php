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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id'); // PK
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນ (FK)'); // FK to classes, NOT NULL
            $table->unsignedBigInteger('subject_id')->comment('ລະຫັດວິຊາ (FK)'); // FK to subjects, NOT NULL
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('ລະຫັດຄູສອນ (FK)'); // FK to teachers, Nullable
            $table->string('day_of_week', 20)->comment('ມື້ໃນອາທິດ (ຕົວຢ່າງ: Monday)'); // NOT NULL
            $table->time('start_time')->comment('ເວລາເລີ່ມສອນ'); // NOT NULL
            $table->time('end_time')->comment('ເວລາເລີກສອນ'); // NOT NULL
            $table->string('room', 50)->nullable()->comment('ຫ້ອງ ຫຼື ສະຖານທີ່ສອນ');
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->boolean('is_active')->default(true)->comment('ສະຖານະຕາຕະລາງ (TRUE = ໃຊ້ງານ)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('cascade');

            $table->foreign('subject_id')
                  ->references('subject_id')->on('subjects')
                  ->onDelete('cascade');

            $table->foreign('teacher_id')
                  ->references('teacher_id')->on('teachers')
                  ->onDelete('set null');

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict');

            // Define Unique Constraints for Clash Detection
            $table->unique(['academic_year_id', 'class_id', 'day_of_week', 'start_time'], 'UQ_Schedules_class_time');
                // ->comment('Prevent class time clash');

            $table->unique(['academic_year_id', 'room', 'day_of_week', 'start_time'], 'UQ_Schedules_room_time');
                // ->comment('Prevent room time clash (when room is not NULL)');

            // Note: Teacher clash detection (UQ on teacher_id, day, time) needs careful handling
            // due to nullable teacher_id. Database unique constraints might ignore NULLs.
            // Consider application-level validation or database-specific features if strict enforcement is needed.

            // Define Indexes
            $table->index('class_id', 'IDX_Schedules_class');
            $table->index('subject_id', 'IDX_Schedules_subject');
            $table->index('teacher_id', 'IDX_Schedules_teacher');
            $table->index('academic_year_id', 'IDX_Schedules_acad_year');
            $table->index('day_of_week', 'IDX_Schedules_day');
            $table->index('room', 'IDX_Schedules_room');
            $table->index('is_active', 'IDX_Schedules_active');

            // Note: Application logic should validate end_time > start_time.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
