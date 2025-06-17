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
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id('fee_type_id'); // PK
            $table->string('fee_name', 100)->unique()->comment('ຊື່ປະເພດຄ່າທຳນຽມ (ເຊັ່ນ: ຄ່າຮຽນ)'); // Unique, NOT NULL
            $table->text('fee_description')->nullable()->comment('ຄຳອະທິບາຍ');
            $table->decimal('amount', 10, 2)->comment('ຈຳນວນເງິນມາດຕະຖານ'); // NOT NULL
            $table->boolean('is_recurring')->default(false)->comment('ເປັນຄ່າທຳນຽມທີ່ເກັບປະຈຳ? (TRUE/FALSE)'); // NOT NULL, default false
            $table->enum('recurring_interval', ['monthly', 'quarterly', 'yearly'])->nullable()->comment('ໄລຍະເກັບ (ຖ້າເກັບປະຈຳ)');
            $table->boolean('is_mandatory')->default(true)->comment('ເປັນຄ່າທຳນຽມບັງຄັບຈ່າຍ? (TRUE/FALSE)'); // NOT NULL, default true
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE=ໃຊ້ງານ)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined in this table definition

            // Unique constraint on fee_name defined inline

            // Indexes for performance
            $table->index('is_recurring', 'IDX_FeeTypes_recurring');
            $table->index('is_mandatory', 'IDX_FeeTypes_mandatory');
            $table->index('is_active', 'IDX_FeeTypes_active');

            // Note: Application logic should validate amount >= 0 and
            // consistency between is_recurring and recurring_interval
            // (e.g., recurring_interval is required if is_recurring is true).
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
