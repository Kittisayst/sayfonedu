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
        Schema::create('parents', function (Blueprint $table) {
            $table->id('parent_id'); // PK
            $table->string('first_name_lao', 100)->comment('ຊື່ຜູ້ປົກຄອງ (ພາສາລາວ)'); // NOT NULL
            $table->string('last_name_lao', 100)->comment('ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາລາວ)'); // NOT NULL
            $table->string('first_name_en', 100)->nullable()->comment('ຊື່ຜູ້ປົກຄອງ (ພາສາອັງກິດ)');
            $table->string('last_name_en', 100)->nullable()->comment('ນາມສະກຸນຜູ້ປົກຄອງ (ພາສາອັງກິດ)');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->comment('ເພດ');
            $table->date('date_of_birth')->nullable()->comment('ວັນເດືອນປີເກີດ');
            $table->string('national_id', 50)->nullable()->unique()->comment('ເລກບັດປະຈຳຕົວ');
            $table->string('occupation', 100)->nullable()->comment('ອາຊີບ');
            $table->string('workplace', 255)->nullable()->comment('ສະຖານທີ່ເຮັດວຽກ');
            $table->string('education_level', 100)->nullable()->comment('ລະດັບການສຶກສາ');
            $table->string('income_level', 100)->nullable()->comment('ລະດັບລາຍຮັບ (ອາດຈະບໍ່ເກັບ)');
            $table->string('phone', 20)->comment('ເບີໂທລະສັບຫຼັກ'); // NOT NULL
            $table->string('alternative_phone', 20)->nullable()->comment('ເບີໂທລະສັບສຳຮອງ');
            $table->string('email', 100)->nullable()->unique()->comment('ອີເມວ');
            $table->unsignedBigInteger('village_id')->nullable()->comment('ລະຫັດບ້ານ (FK)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('ລະຫັດເມືອງ (FK)');
            $table->unsignedBigInteger('province_id')->nullable()->comment('ລະຫັດແຂວງ (FK)');
            $table->text('address')->nullable()->comment('ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ)');
            $table->unsignedBigInteger('user_id')->nullable()->unique()->comment('ລະຫັດບັນຊີຜູ້ໃຊ້ (FK)');
            $table->string('profile_image', 255)->nullable()->comment('ທີ່ຢູ່ຮູບໂປຣໄຟລ໌');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('village_id')->references('village_id')->on('villages')->onDelete('set null');
            $table->foreign('district_id')->references('district_id')->on('districts')->onDelete('set null');
            $table->foreign('province_id')->references('province_id')->on('provinces')->onDelete('set null');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');

            // Define Indexes
            $table->index(['last_name_lao', 'first_name_lao'], 'IDX_Parents_name_lao');
            $table->index(['last_name_en', 'first_name_en'], 'IDX_Parents_name_en');
            $table->index('village_id', 'IDX_Parents_village');
            $table->index('district_id', 'IDX_Parents_district');
            $table->index('province_id', 'IDX_Parents_province');
            // Indexes for unique keys (national_id, email, user_id) are created automatically by ->unique()
            $table->index('phone', 'IDX_Parents_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
