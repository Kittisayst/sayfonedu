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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id('discount_id'); // PK
            $table->string('discount_name', 100)->unique()->comment('ຊື່ສ່ວນຫຼຸດ (ຕົວຢ່າງ: ສ່ວນຫຼຸດພີ່ນ້ອງ)'); // Unique, NOT NULL
            $table->enum('discount_type', ['percentage', 'fixed'])->comment('ປະເພດສ່ວນຫຼຸດ: percentage, fixed'); // NOT NULL
            $table->decimal('discount_value', 10, 2)->comment('ຄ່າຂອງສ່ວນຫຼຸດ (ເປີເຊັນ 0-100 ຫຼື ຈຳນວນເງິນ)'); // NOT NULL
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍ ຫຼື ເງື່ອນໄຂຂອງສ່ວນຫຼຸດ');
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE = ໃຊ້ງານໄດ້)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined for this table

            // Unique constraint on discount_name defined inline

            // Indexes for performance
            $table->index('discount_type', 'IDX_Discounts_type');
            $table->index('is_active', 'IDX_Discounts_active');

            // Note: Application logic should validate discount_value range
            // (e.g., >= 0, and <= 100 if type is 'percentage').
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
