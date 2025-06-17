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
        Schema::create('classes', function (Blueprint $table) {
            $table->id('class_id'); // PK
            $table->string('class_name', 100)->comment('ຊື່ຫ້ອງຮຽນ (ຕົວຢ່າງ: ມ.1/1)'); // NOT NULL
            $table->string('grade_level', 50)->comment('ລະດັບຊັ້ນ (ຕົວຢ່າງ: ມ.1)'); // NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->unsignedBigInteger('homeroom_teacher_id')->nullable()->comment('ລະຫັດຄູປະຈຳຫ້ອງ (FK)'); // FK to teachers, Nullable
            $table->string('room_number', 20)->nullable()->comment('ເລກຫ້ອງ ຫຼື ສະຖານທີ່ຂອງຫ້ອງຮຽນ');
            $table->integer('capacity')->nullable()->comment('ຈຳນວນນັກຮຽນສູງສຸດທີ່ຮອງຮັບໄດ້');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບຫ້ອງຮຽນ');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('ສະຖານະຫ້ອງຮຽນ'); // NOT NULL, default active
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('academic_year_id')
                ->references('academic_year_id')->on('academic_years')
                ->onDelete('restrict'); // Prevent deleting academic year if classes exist

            $table->foreign('homeroom_teacher_id')
                ->references('teacher_id')->on('teachers')
                ->onDelete('set null'); // Allow class to exist if homeroom teacher deleted

            // Define Unique Constraint
            $table->unique(['academic_year_id', 'class_name'], 'UQ_Classes_name_year');
            // ->comment('Class name must be unique within an academic year'); // Comment for unique constraint

            // Define Indexes
            $table->index('academic_year_id', 'IDX_Classes_academic_year');
            $table->index('homeroom_teacher_id', 'IDX_Classes_teacher');
            $table->index('grade_level', 'IDX_Classes_grade_level');
            $table->index('status', 'IDX_Classes_status');

            // Note: Application logic should validate capacity > 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
