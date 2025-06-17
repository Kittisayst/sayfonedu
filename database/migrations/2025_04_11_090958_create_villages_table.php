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
        Schema::create('villages', function (Blueprint $table) {
            $table->id('village_id');
            $table->string('village_name_lao', 100)->comment('ຊື່ບ້ານ (ພາສາລາວ)');
            $table->string('village_name_en', 100)->nullable()->comment('ຊື່ບ້ານ (ພາສາອັງກິດ)');
            $table->unsignedBigInteger('district_id')->comment('ລະຫັດເມືອງທີ່ບ້ານນີ້ສັງກັດ (FK ຈາກ Districts)');
            $table->timestamps();
            
            $table->unique(['district_id', 'village_name_lao'], 'UQ_Villages_district_name_lao');
            $table->unique(['district_id', 'village_name_en'], 'UQ_Villages_district_name_en');
            
            $table->foreign('district_id')->references('district_id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
