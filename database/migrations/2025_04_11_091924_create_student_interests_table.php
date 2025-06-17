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
        Schema::create('student_interests', function (Blueprint $table) {
            $table->id('interest_id');
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK ຈາກ Students)');
            $table->string('interest_category', 100)->nullable()->comment('ໝວດໝູ່ຄວາມສົນໃຈ (ເຊັ່ນ: ກິລາ, ດົນຕີ, ສິລະປະ, ວິຊາການ)');
            $table->string('interest_name', 255)->comment('ຊື່ຄວາມສົນໃຈສະເພາະ (ເຊັ່ນ: ບານເຕະ, ເປຍໂນ, ແຕ້ມຮູບ, Math Club)');
            $table->text('description')->nullable()->comment('ລາຍລະອຽດເພີ່ມເຕີມກ່ຽວກັບຄວາມສົນໃຈນີ້');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            
            // Unique constraint
            $table->unique(['student_id', 'interest_name'], 'UQ_StudInterests_student_interest');
            
            // Indexes
            $table->index(['student_id'], 'IDX_StudInterests_student');
            $table->index(['interest_category'], 'IDX_StudInterests_category');
            $table->index(['interest_name'], 'IDX_StudInterests_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_interests');
    }
};
