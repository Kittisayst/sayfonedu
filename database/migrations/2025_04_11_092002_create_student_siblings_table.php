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
        Schema::create('student_siblings', function (Blueprint $table) {
            $table->id('sibling_id');
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນຄົນທີໜຶ່ງ (FK ຈາກ Students)');
            $table->unsignedBigInteger('sibling_student_id')->comment('ລະຫັດນັກຮຽນຄົນທີສອງ (ພີ່ນ້ອງ) (FK ຈາກ Students)');
            $table->enum('relationship', ['brother', 'sister', 'step_brother', 'step_sister'])->comment('ຄວາມສຳພັນ: brother, sister, step_brother, step_sister');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('sibling_student_id')->references('student_id')->on('students')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['student_id', 'sibling_student_id'], 'UQ_StudSiblings_pair');
            
            // Indexes
            $table->index(['student_id'], 'IDX_StudSiblings_student');
            $table->index(['sibling_student_id'], 'IDX_StudSiblings_sibling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_siblings');
    }
};
