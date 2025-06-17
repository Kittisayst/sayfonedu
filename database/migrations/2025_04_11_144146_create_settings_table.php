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
        Schema::create('settings', function (Blueprint $table) {
            $table->id('setting_id'); // PK
            $table->string('setting_key', 100)->unique()->comment('ຊື່ Key ຂອງການຕັ້ງຄ່າ (ຕ້ອງບໍ່ຊ້ຳ)'); // Unique, NOT NULL
            $table->text('setting_value')->nullable()->comment('ຄ່າຂອງການຕັ້ງຄ່າ (ເກັບເປັນ Text)');
            $table->string('setting_group', 50)->nullable()->comment('ກຸ່ມຂອງການຕັ້ງຄ່າ (ເພື່ອຈັດໝວດໝູ່)');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍວ່າການຕັ້ງຄ່ານີ້ແມ່ນຫຍັງ');
            $table->boolean('is_system')->default(false)->comment('ເປັນການຕັ້ງຄ່າຫຼັກຂອງລະບົບ?'); // NOT NULL, default false
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined for this table

            // Unique constraint on setting_key defined inline

            // Define Indexes
            $table->index('setting_group', 'IDX_Settings_group');
            $table->index('is_system', 'IDX_Settings_system');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
