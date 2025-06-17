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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->string('permission_name', 100)->unique()->comment('ຊື່ສິດທິ (ເຊັ່ນ: create_user, edit_grades, view_reports)');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍວ່າສິດທິນີ້ອະນຸຍາດໃຫ້ເຮັດຫຍັງ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
