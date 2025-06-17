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
        Schema::create('student_parent', function (Blueprint $table) {
            $table->id('student_parent_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students
            $table->unsignedBigInteger('parent_id')->comment('ລະຫັດຜູ້ປົກຄອງ (FK)'); // FK to parents
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])->comment('ຄວາມສຳພັນ (ພໍ່, ແມ່, ຜູ້ປົກຄອງ, ອື່ນໆ)'); // NOT NULL
            $table->boolean('is_primary_contact')->nullable()->default(false)->comment('ເປັນຜູ້ຕິດຕໍ່ຫຼັກ ຫຼື ບໍ່ (TRUE/FALSE)');
            $table->boolean('has_custody')->nullable()->default(true)->comment('ມີສິດໃນການດູແລ ຫຼື ບໍ່ (TRUE/FALSE)');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, remove link

            $table->foreign('parent_id')
                  ->references('parent_id')->on('parents')
                  ->onDelete('cascade'); // If parent deleted, remove link

            // Define Unique Constraint
            $table->unique(['student_id', 'parent_id'], 'UQ_StudentParent_pair');
                // ->comment('Prevent linking the same student and parent more than once'); // Comment for unique constraint

            // Define Indexes
            $table->index('student_id', 'IDX_StudentParent_student');
            $table->index('parent_id', 'IDX_StudentParent_parent');
            $table->index('is_primary_contact', 'IDX_StudentParent_primary');

            // Note: Application logic should enforce that only one parent per student has is_primary_contact = TRUE.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_parent');
    }
};
