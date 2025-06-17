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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id('province_id');
            $table->string('province_name_lao', 100)->unique()->comment('ຊື່ແຂວງ (ພາສາລາວ)');
            $table->string('province_name_en', 100)->nullable()->unique()->comment('ຊື່ແຂວງ (ພາສາອັງກິດ)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provinces');
    }
};
