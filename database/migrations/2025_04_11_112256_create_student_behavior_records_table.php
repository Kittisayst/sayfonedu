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
        Schema::create('student_behavior_records', function (Blueprint $table) {
            $table->id('behavior_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to Students
            $table->enum('record_type', ['positive', 'negative', 'neutral'])->comment('ປະເພດພຶດຕິກຳ (ບວກ, ລົບ, ກາງ)'); // NOT NULL
            $table->text('description')->comment('ລາຍລະອຽດພຶດຕິກຳທີ່ສັງເກດເຫັນ'); // NOT NULL
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('ລະຫັດຄູສອນ ຫຼື ຜູ້ບັນທຶກ (FK)'); // FK to Teachers, Nullable
            $table->date('record_date')->comment('ວັນທີທີ່ສັງເກດເຫັນ/ບັນທຶກ'); // NOT NULL
            $table->text('action_taken')->nullable()->comment('ການດຳເນີນການທີ່ໄດ້ເຮັດໄປແລ້ວ (ຖ້າມີ)');
            $table->text('follow_up')->nullable()->comment('ການຕິດຕາມຜົນ ຫຼື ໝາຍເຫດເພີ່ມເຕີມ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                ->references('student_id')->on('students')
                ->onDelete('cascade'); // If student deleted, delete behavior record

            $table->foreign('teacher_id')
                ->references('teacher_id')->on('teachers')
                ->onDelete('set null'); // If teacher deleted, keep record but set teacher_id to NULL

            // Define Indexes
            $table->index('student_id', 'IDX_StudBehavior_student');
            $table->index('teacher_id', 'IDX_StudBehavior_teacher');
            $table->index('record_type', 'IDX_StudBehavior_type');
            $table->index('record_date', 'IDX_StudBehavior_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_behavior_records');
    }
};
