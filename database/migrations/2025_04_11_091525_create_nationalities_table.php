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
        Schema::create('nationalities', function (Blueprint $table) {
            $table->id('nationality_id');
            $table->string('nationality_name_lao', 100)->unique()->comment('ຊື່ສັນຊາດ (ພາສາລາວ)');
            $table->string('nationality_name_en', 100)->nullable()->unique()->comment('ຊື່ສັນຊາດ (ພາສາອັງກິດ)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nationalities');
    }
};
