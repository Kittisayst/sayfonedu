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
        Schema::create('student_previous_locations', function (Blueprint $table) {
            $table->id('location_id');
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK ຈາກ Students)');
            $table->text('address')->nullable()->comment('ທີ່ຢູ່ລະອຽດ (ເລກເຮືອນ, ຮ່ອມ...)');
            $table->unsignedBigInteger('village_id')->nullable()->comment('ລະຫັດບ້ານ (FK ຈາກ Villages). ອາດຈະ NULL ຖ້າຢູ່ນອກ ຫຼື ບໍ່ຮູ້.');
            $table->unsignedBigInteger('district_id')->nullable()->comment('ລະຫັດເມືອງ (FK ຈາກ Districts). ອາດຈະ NULL.');
            $table->unsignedBigInteger('province_id')->nullable()->comment('ລະຫັດແຂວງ (FK ຈາກ Provinces). ອາດຈະ NULL.');
            $table->string('country', 100)->default('Laos')->comment('ປະເທດ');
            $table->date('from_date')->nullable()->comment('ວັນທີທີ່ເລີ່ມອາໄສຢູ່ທີ່ຢູ່ນີ້');
            $table->date('to_date')->nullable()->comment('ວັນທີທີ່ຍ້າຍອອກຈາກທີ່ຢູ່ນີ້');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('village_id')->references('village_id')->on('villages');
            $table->foreign('district_id')->references('district_id')->on('districts');
            $table->foreign('province_id')->references('province_id')->on('provinces');
            
            // Indexes
            $table->index(['student_id'], 'IDX_StudPrevLoc_student');
            $table->index(['village_id'], 'IDX_StudPrevLoc_village');
            $table->index(['district_id'], 'IDX_StudPrevLoc_district');
            $table->index(['province_id'], 'IDX_StudPrevLoc_province');
            $table->index(['country'], 'IDX_StudPrevLoc_country');
            $table->index(['from_date', 'to_date'], 'IDX_StudPrevLoc_dates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_previous_locations');
    }
};
