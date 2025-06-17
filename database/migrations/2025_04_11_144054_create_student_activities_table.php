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
        Schema::create('student_activities', function (Blueprint $table) {
            $table->id('student_activity_id'); // PK
            $table->unsignedBigInteger('activity_id')->comment('ລະຫັດກິດຈະກຳ (FK)'); // FK to extracurricular_activities, NOT NULL
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->date('join_date')->nullable()->comment('ວັນທີເຂົ້າຮ່ວມ/ລົງທະບຽນ');
            $table->enum('status', ['active', 'completed', 'dropped'])->default('active')->comment('ສະຖານະ: active, completed, dropped'); // NOT NULL, default active
            $table->string('performance', 100)->nullable()->comment('ຜົນງານ/ລະດັບການເຂົ້າຮ່ວມ (ຖ້າມີ)');
            $table->text('notes')->nullable()->comment('ໝາຍເຫດເພີ່ມເຕີມ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('activity_id')
                  ->references('activity_id')->on('extracurricular_activities')
                  ->onDelete('cascade'); // If activity deleted, delete participation record

            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, delete participation record

            // Define Unique Constraint
            $table->unique(['student_id', 'activity_id'], 'UQ_StudAct_student_activity');
                // ->comment('Prevent a student from joining the same activity twice');

            // Define Indexes
            $table->index('activity_id', 'IDX_StudAct_activity');
            $table->index('student_id', 'IDX_StudAct_student');
            $table->index('status', 'IDX_StudAct_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_activities');
    }
};
