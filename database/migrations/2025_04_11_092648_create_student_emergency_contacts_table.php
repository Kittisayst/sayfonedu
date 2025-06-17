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
        Schema::create('student_emergency_contacts', function (Blueprint $table) {
            $table->id('contact_id'); // Corresponds to contact_id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // Foreign key column
            $table->string('contact_name', 255)->comment('ຊື່ ແລະ ນາມສະກຸນຂອງຜູ້ຕິດຕໍ່'); // NOT NULL by default
            $table->string('relationship', 100)->nullable()->comment('ຄວາມສຳພັນກັບນັກຮຽນ (ເຊັ່ນ: ພໍ່, ແມ່)');
            $table->string('phone', 20)->comment('ເບີໂທລະສັບຫຼັກທີ່ຕິດຕໍ່ໄດ້'); // NOT NULL by default
            $table->string('alternative_phone', 20)->nullable()->comment('ເບີໂທລະສັບສຳຮອງ (ຖ້າມີ)');
            $table->text('address')->nullable()->comment('ທີ່ຢູ່ຂອງຜູ້ຕິດຕໍ່ (ຖ້າມີ)');
            $table->integer('priority')->nullable()->default(1)->comment('ລຳດັບຄວາມສຳຄັນໃນການຕິດຕໍ່ (1 = ຕິດຕໍ່ກ່ອນ)');
            $table->timestamps(); // Adds nullable created_at and updated_at TIMESTAMP columns

            // Define Foreign Key
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student is deleted, delete related emergency contacts

            // Define Indexes
            $table->index('student_id', 'IDX_StudEmergContacts_student'); // Index for searching by student
            $table->index(['student_id', 'priority'], 'IDX_StudEmergContacts_priority'); // Index for sorting contacts by priority for a student
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_emergency_contacts');
    }
};
