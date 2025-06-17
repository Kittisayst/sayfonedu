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
        Schema::create('ethnicities', function (Blueprint $table) {
            $table->id('ethnicity_id');
            $table->string('ethnicity_name_lao', 100)->unique()->comment('ຊື່ຊົນເຜົ່າ (ພາສາລາວ)');
            $table->string('ethnicity_name_en', 100)->nullable()->unique()->comment('ຊື່ຊົນເຜົ່າ (ພາສາອັງກິດ)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnicities');
    }
};
