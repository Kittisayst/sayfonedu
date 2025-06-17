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
        Schema::create('religions', function (Blueprint $table) {
            $table->id('religion_id');
            $table->string('religion_name_lao', 100)->unique()->comment('ຊື່ສາສະໜາ (ພາສາລາວ)');
            $table->string('religion_name_en', 100)->nullable()->unique()->comment('ຊື່ສາສະໜາ (ພາສາອັງກິດ)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('religions');
    }
};
