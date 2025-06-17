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
        Schema::create('districts', function (Blueprint $table) {
            $table->id('district_id');
            $table->string('district_name_lao', 100)->comment('ຊື່ເມືອງ (ພາສາລາວ)');
            $table->string('district_name_en', 100)->nullable()->comment('ຊື່ເມືອງ (ພາສາອັງກິດ)');
            $table->unsignedBigInteger('province_id')->comment('ລະຫັດແຂວງທີ່ເມືອງນີ້ສັງກັດ (FK ຈາກ Provinces)');
            $table->timestamps();
            
            $table->unique(['province_id', 'district_name_lao'], 'UQ_Districts_province_name_lao');
            $table->unique(['province_id', 'district_name_en'], 'UQ_Districts_province_name_en');
            
            $table->foreign('province_id')->references('province_id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
