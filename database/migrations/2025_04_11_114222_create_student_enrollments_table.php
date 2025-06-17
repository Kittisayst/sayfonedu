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
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id('enrollment_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນທີ່ລົງທະບຽນ (FK)'); // FK to classes, NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນທີ່ລົງທະບຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->date('enrollment_date')->comment('ວັນທີລົງທະບຽນເຂົ້າຫ້ອງນີ້'); // NOT NULL
            $table->enum('enrollment_status', ['enrolled', 'transferred', 'dropped'])->default('enrolled')->comment('ສະຖານະ: enrolled, transferred, dropped'); // NOT NULL, default enrolled
            $table->unsignedBigInteger('previous_class_id')->nullable()->comment('ລະຫັດຫ້ອງຮຽນກ່ອນໜ້າ (FK)'); // Nullable FK to classes
            $table->boolean('is_new_student')->default(false)->comment('ເປັນນັກຮຽນໃໝ່ໃນສົກຮຽນນີ້'); // NOT NULL, default false
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, delete enrollment

            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('restrict'); // Prevent deleting class if students enrolled

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict'); // Prevent deleting academic year if students enrolled

            $table->foreign('previous_class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('set null'); // If previous class deleted, set FK to NULL

            // Define Unique Constraint
            $table->unique(['student_id', 'academic_year_id'], 'UQ_StudEnroll_student_year');
                // ->comment('One main enrollment record per student per academic year');

            // Define Indexes
            $table->index('student_id', 'IDX_StudEnroll_student');
            $table->index('class_id', 'IDX_StudEnroll_class');
            $table->index('academic_year_id', 'IDX_StudEnroll_acad_year');
            $table->index('previous_class_id', 'IDX_StudEnroll_prev_class');
            $table->index('enrollment_status', 'IDX_StudEnroll_status');

            // Note: UQ constraint assumes one main enrollment record per student per year.
            // Transfers might require updating this record or different handling logic.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
