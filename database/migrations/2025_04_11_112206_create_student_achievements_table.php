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
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id('achievement_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK
            $table->string('achievement_type', 100)->nullable()->comment('ປະເພດຜົນງານ (ເຊັ່ນ: ວິຊາການ, ກິລາ, ສິລະປະ)');
            $table->string('title', 255)->comment('ຊື່ຜົນງານ ຫຼື ລາງວັນທີ່ໄດ້ຮັບ'); // NOT NULL
            $table->text('description')->nullable()->comment('ລາຍລະອຽດກ່ຽວກັບຜົນງານ ຫຼື ລາງວັນ');
            $table->date('award_date')->nullable()->comment('ວັນທີທີ່ໄດ້ຮັບລາງວັນ/ຜົນງານ');
            $table->string('issuer', 255)->nullable()->comment('ຜູ້ມອບລາງວັນ ຫຼື ໜ່ວຍງານທີ່ຈັດ');
            $table->string('certificate_path', 255)->nullable()->comment('ທີ່ຢູ່ໄຟລ໌ໃບຢັ້ງຢືນ/ຮູບພາບ (ຖ້າມີ)');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, delete their achievements

            // Define Indexes
            $table->index('student_id', 'IDX_StudAchieve_student');
            $table->index('achievement_type', 'IDX_StudAchieve_type');
            $table->index('award_date', 'IDX_StudAchieve_date');
            $table->index('issuer', 'IDX_StudAchieve_issuer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
    }
};
