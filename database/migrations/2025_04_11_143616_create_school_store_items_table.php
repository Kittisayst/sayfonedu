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
        Schema::create('school_store_items', function (Blueprint $table) {
            $table->id('item_id'); // PK
            $table->string('item_name', 255)->unique()->comment('ຊື່ສິນຄ້າ'); // Unique, NOT NULL
            $table->string('item_code', 50)->nullable()->unique()->comment('ລະຫັດສິນຄ້າ/ບາໂຄດ'); // Nullable, Unique
            $table->string('category', 100)->nullable()->comment('ໝວດໝູ່ສິນຄ້າ (ຕົວຢ່າງ: ເຄື່ອງຂຽນ)');
            $table->text('description')->nullable()->comment('ລາຍລະອຽດສິນຄ້າ');
            $table->decimal('unit_price', 10, 2)->comment('ລາຄາຂາຍຕໍ່ໜ່ວຍ'); // NOT NULL
            $table->integer('stock_quantity')->default(0)->comment('ຈຳນວນສິນຄ້າທີ່ມີໃນສາງ'); // NOT NULL, default 0
            $table->integer('reorder_level')->nullable()->comment('ລະດັບຄົງເຫຼືອຂັ້ນຕ່ຳທີ່ຄວນສັ່ງຊື້ໃໝ່');
            $table->string('item_image', 255)->nullable()->comment('ທີ່ຢູ່ໄຟລ໌ຮູບພາບສິນຄ້າ');
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE = ຍັງມີຂາຍ/ໃຊ້ງານ)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined for this table

            // Unique constraints defined inline

            // Indexes for performance
            $table->index('category', 'IDX_StoreItems_category');
            $table->index('is_active', 'IDX_StoreItems_active');

            // Note: Application logic should validate unit_price >= 0,
            // stock_quantity >= 0, and reorder_level (if not null) >= 0.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_store_items');
    }
};
