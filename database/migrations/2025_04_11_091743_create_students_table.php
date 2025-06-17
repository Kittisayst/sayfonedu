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
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->string('student_code', 20)->unique()->comment('ລະຫັດປະຈຳຕົວນັກຮຽນ');
            $table->string('first_name_lao', 100)->comment('ຊື່ນັກຮຽນ (ພາສາລາວ)');
            $table->string('last_name_lao', 100)->comment('ນາມສະກຸນນັກຮຽນ (ພາສາລາວ)');
            $table->string('first_name_en', 100)->nullable()->comment('ຊື່ນັກຮຽນ (ພາສາອັງກິດ)');
            $table->string('last_name_en', 100)->nullable()->comment('ນາມສະກຸນນັກຮຽນ (ພາສາອັງກິດ)');
            $table->string('nickname', 100)->nullable()->comment('ຊື່ຫຼິ້ນ');
            $table->enum('gender', ['male', 'female', 'other'])->comment('ເພດ: male, female, other');
            $table->date('date_of_birth')->comment('ວັນເດືອນປີເກີດ');
            $table->unsignedBigInteger('nationality_id')->nullable()->comment('ລະຫັດສັນຊາດ (FK ຈາກ Nationalities)');
            $table->unsignedBigInteger('religion_id')->nullable()->comment('ລະຫັດສາສະໜາ (FK ຈາກ Religions)');
            $table->unsignedBigInteger('ethnicity_id')->nullable()->comment('ລະຫັດຊົນເຜົ່າ (FK ຈາກ Ethnicities)');
            $table->unsignedBigInteger('village_id')->nullable()->comment('ລະຫັດບ້ານ (FK ຈາກ Villages)');
            $table->unsignedBigInteger('district_id')->nullable()->comment('ລະຫັດເມືອງ (FK ຈາກ Districts)');
            $table->unsignedBigInteger('province_id')->nullable()->comment('ລະຫັດແຂວງ (FK ຈາກ Provinces)');
            $table->text('current_address')->nullable()->comment('ທີ່ຢູ່ປັດຈຸບັນ (ລາຍລະອຽດ ເລກເຮືອນ, ຮ່ອມ, ...)');
            $table->string('profile_image', 255)->nullable()->comment('ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'unknown'])->default('unknown')->comment('ກຸ່ມເລືອດ: A, B, AB, O, unknown');
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active')->comment('ສະຖານະນັກຮຽນ: active, inactive, graduated, transferred');
            $table->unsignedBigInteger('user_id')->nullable()->unique()->comment('ລະຫັດບັນຊີຜູ້ໃຊ້ຂອງນັກຮຽນ (FK ຈາກ Users). ອາດຈະ NULL ຖ້ານັກຮຽນຍັງບໍ່ມີບັນຊີ.');
            $table->date('admission_date')->comment('ວັນທີເຂົ້າຮຽນ');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('nationality_id')->references('nationality_id')->on('nationalities');
            $table->foreign('religion_id')->references('religion_id')->on('religions');
            $table->foreign('ethnicity_id')->references('ethnicity_id')->on('ethnicities');
            $table->foreign('village_id')->references('village_id')->on('villages');
            $table->foreign('district_id')->references('district_id')->on('districts');
            $table->foreign('province_id')->references('province_id')->on('provinces');
            $table->foreign('user_id')->references('user_id')->on('users');
            
            // Indexes
            $table->index(['last_name_lao', 'first_name_lao'], 'IDX_Students_name_lao');
            $table->index(['last_name_en', 'first_name_en'], 'IDX_Students_name_en');
            $table->index(['nationality_id'], 'IDX_Students_nationality');
            $table->index(['religion_id'], 'IDX_Students_religion');
            $table->index(['ethnicity_id'], 'IDX_Students_ethnicity');
            $table->index(['village_id'], 'IDX_Students_village');
            $table->index(['district_id'], 'IDX_Students_district');
            $table->index(['province_id'], 'IDX_Students_province');
            $table->index(['user_id'], 'IDX_Students_user');
            $table->index(['status'], 'IDX_Students_status');
            $table->index(['admission_date'], 'IDX_Students_admission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
