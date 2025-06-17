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
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id('class_subject_id'); // PK
            $table->unsignedBigInteger('class_id')->comment('ລະຫັດຫ້ອງຮຽນ (FK)'); // FK to classes
            $table->unsignedBigInteger('subject_id')->comment('ລະຫັດວິຊາຮຽນ (FK)'); // FK to subjects
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('ລະຫັດຄູສອນທີ່ຮັບຜິດຊອບ (FK)'); // FK to teachers, Nullable
            $table->integer('hours_per_week')->nullable()->comment('ຈຳນວນຊົ່ວໂມງຕໍ່ອາທິດ (ໂດຍປະມານ)');
            $table->string('day_of_week', 20)->nullable()->comment('ມື້ທີ່ສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)');
            $table->time('start_time')->nullable()->comment('ເວລາເລີ່ມສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)');
            $table->time('end_time')->nullable()->comment('ເວລາເລີກສອນ (ຂໍ້ມູນເບື້ອງຕົ້ນ)');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('ສະຖານະການມອບໝາຍ'); // NOT NULL, default active
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('class_id')
                  ->references('class_id')->on('classes')
                  ->onDelete('cascade'); // If class deleted, remove assignment

            $table->foreign('subject_id')
                  ->references('subject_id')->on('subjects')
                  ->onDelete('cascade'); // If subject deleted, remove assignment

            $table->foreign('teacher_id')
                  ->references('teacher_id')->on('teachers')
                  ->onDelete('set null'); // If teacher deleted, keep assignment but set teacher to NULL

            // Define Unique Constraint
            $table->unique(['class_id', 'subject_id'], 'UQ_ClassSubjects_class_subj');
                // ->comment('A subject should only be assigned once per class');

            // Define Indexes
            $table->index('class_id', 'IDX_ClassSubjects_class');
            $table->index('subject_id', 'IDX_ClassSubjects_subject');
            $table->index('teacher_id', 'IDX_ClassSubjects_teacher');
            $table->index('status', 'IDX_ClassSubjects_status');

            // Note: Application logic should validate hours_per_week >= 0 and time sequence if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};
