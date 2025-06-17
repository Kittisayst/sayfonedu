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
        Schema::create('student_health_records', function (Blueprint $table) {
            $table->id('health_id'); // Corresponds to health_id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK ຈາກ Students)'); // Foreign key column
            $table->string('health_condition', 255)->nullable()->comment('ສະພາບສຸຂະພາບທົ່ວໄປ ຫຼື ພະຍາດປະຈຳຕົວ');
            $table->text('medications')->nullable()->comment('ລາຍການຢາທີ່ນັກຮຽນໃຊ້ປະຈຳ');
            $table->text('allergies')->nullable()->comment('ປະຫວັດການແພ້ຢາ/ອາຫານ');
            $table->text('special_needs')->nullable()->comment('ຄວາມຕ້ອງການພິເສດດ້ານສຸຂະພາບ');
            $table->string('doctor_name', 100)->nullable()->comment('ຊື່ແພດປະຈຳຕົວ (ຖ້າມີ)');
            $table->string('doctor_phone', 20)->nullable()->comment('ເບີໂທແພດປະຈຳຕົວ (ຖ້າມີ)');
            $table->date('record_date')->comment('ວັນທີບັນທຶກ/ອັບເດດຂໍ້ມູນ'); // Corresponds to DATE NOT NULL
            $table->timestamps(); // Adds nullable created_at and updated_at TIMESTAMP columns

            // Define Foreign Key
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student is deleted, delete related health records

            // Define Indexes
            $table->index('student_id', 'IDX_StudHealth_student'); // Index for searching by student
            $table->index('record_date', 'IDX_StudHealth_record_date'); // Index for searching by date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_health_records');
    }
};
