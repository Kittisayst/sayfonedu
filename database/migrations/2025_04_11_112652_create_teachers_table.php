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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id('teacher_id'); // PK
            $table->string('teacher_code', 20)->unique()->comment('ລະຫັດປະຈຳຕົວຄູສອນ'); // Unique, NOT NULL
            $table->string('first_name_lao', 100)->comment('ຊື່ຄູສອນ (ພາສາລາວ)'); // NOT NULL
            $table->string('last_name_lao', 100)->comment('ນາມສະກຸນຄູສອນ (ພາສາລາວ)'); // NOT NULL
            $table->string('first_name_en', 100)->nullable()->comment('ຊື່ຄູສອນ (ພາສາອັງກິດ)');
            $table->string('last_name_en', 100)->nullable()->comment('ນາມສະກຸນຄູສອນ (ພາສາອັງກິດ)');
            $table->enum('gender', ['male', 'female', 'other'])->comment('ເພດ'); // NOT NULL
            $table->date('date_of_birth')->comment('ວັນເດືອນປີເກີດ'); // NOT NULL
            $table->string('national_id', 50)->nullable()->unique()->comment('ເລກບັດປະຈຳຕົວ');
            $table->string('phone', 20)->comment('ເບີໂທລະສັບຫຼັກ'); // NOT NULL
            $table->string('alternative_phone', 20)->nullable()->comment('ເບີໂທລະສັບສຳຮອງ');
            $table->string('email', 100)->unique()->comment('ອີເມວ (ໃຊ້ສຳລັບເຂົ້າລະບົບ)'); // Unique, NOT NULL
            $table->unsignedBigInteger('village_id')->nullable()->comment('ລະຫັດບ້ານ (FK)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('ລະຫັດເມືອງ (FK)');
            $table->unsignedBigInteger('province_id')->nullable()->comment('ລະຫັດແຂວງ (FK)');
            $table->text('address')->nullable()->comment('ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)');
            $table->string('highest_education', 100)->nullable()->comment('ລະດັບການສຶກສາສູງສຸດ');
            $table->string('specialization', 255)->nullable()->comment('ຄວາມຊຳນານ/ວິຊາເອກ');
            $table->date('employment_date')->comment('ວັນທີເລີ່ມຈ້າງງານ/ເຮັດວຽກ'); // NOT NULL
            $table->enum('contract_type', ['full_time', 'part_time', 'contract'])->nullable()->comment('ປະເພດສັນຍາຈ້າງ');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('ສະຖານະການເຮັດວຽກ'); // NOT NULL with default
            $table->unsignedBigInteger('user_id')->unique()->comment('ລະຫັດບັນຊີຜູ້ໃຊ້ຂອງຄູສອນ (FK)'); // Unique, NOT NULL
            $table->string('profile_image', 255)->nullable()->comment('ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('village_id')->references('village_id')->on('villages')->onDelete('set null');
            $table->foreign('district_id')->references('district_id')->on('districts')->onDelete('set null');
            $table->foreign('province_id')->references('province_id')->on('provinces')->onDelete('set null');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('restrict'); // Prevent deleting User if they are a Teacher

            // Define Indexes
            $table->index(['last_name_lao', 'first_name_lao'], 'IDX_Teachers_name_lao');
            $table->index(['last_name_en', 'first_name_en'], 'IDX_Teachers_name_en');
            $table->index('village_id', 'IDX_Teachers_village');
            $table->index('district_id', 'IDX_Teachers_district');
            $table->index('province_id', 'IDX_Teachers_province');
            // Indexes for teacher_code, national_id, email, user_id created by unique()
            $table->index('status', 'IDX_Teachers_status');
            $table->index('specialization', 'IDX_Teachers_specialization');
            $table->index('employment_date', 'IDX_Teachers_employment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
