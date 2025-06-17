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
        Schema::create('student_previous_education', function (Blueprint $table) {
            $table->id('education_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK
            $table->string('school_name', 255)->comment('ຊື່ໂຮງຮຽນເກົ່າ'); // NOT NULL
            $table->string('education_level', 100)->nullable()->comment('ລະດັບການສຶກສາທີ່ຈົບ (ເຊັ່ນ: ປະຖົມ, ມັດທະຍົມຕົ້ນ)');
            $table->integer('from_year')->nullable()->comment('ປີທີ່ເລີ່ມຮຽນ (ຄ.ສ.)');
            $table->integer('to_year')->nullable()->comment('ປີທີ່ຈົບ (ຄ.ສ.)');
            $table->string('certificate', 255)->nullable()->comment('ຊື່ ຫຼື ທີ່ຢູ່ໄຟລ໌ປະກາດ/ໃບຢັ້ງຢືນ');
            $table->decimal('gpa', 3, 2)->nullable()->comment('ຄະແນນສະເລ່ຍ (GPA)'); // Example: 3.50
            $table->text('description')->nullable()->comment('ໝາຍເຫດ/ລາຍລະອຽດເພີ່ມເຕີມ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, delete this record too

            // Define Indexes
            $table->index('student_id', 'IDX_StudPrevEdu_student');
            $table->index('school_name', 'IDX_StudPrevEdu_school');
            $table->index('education_level', 'IDX_StudPrevEdu_level');

            // Note: Application logic should validate year sequence (to_year >= from_year)
            // and GPA range (e.g., 0.00-4.00) if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_previous_education');
    }
};
