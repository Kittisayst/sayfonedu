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
        Schema::create('store_sales', function (Blueprint $table) {
            $table->id('sale_id'); // PK
            $table->unsignedBigInteger('item_id')->comment('ລະຫັດສິນຄ້າທີ່ຂາຍ (FK)'); // FK to school_store_items, NOT NULL
            $table->integer('quantity')->comment('ຈຳນວນທີ່ຂາຍ'); // NOT NULL
            $table->decimal('unit_price', 10, 2)->comment('ລາຄາຕໍ່ໜ່ວຍ (ໃນເວລາຂາຍ)'); // NOT NULL
            $table->decimal('total_price', 10, 2)->comment('ລາຄາລວມ (ຄຳນວນ: quantity * unit_price)'); // NOT NULL
            $table->decimal('discount', 10, 2)->default(0.00)->comment('ສ່ວນຫຼຸດສຳລັບລາຍການນີ້'); // NOT NULL, default 0.00
            $table->decimal('final_price', 10, 2)->comment('ລາຄາສຸດທິ (ຄຳນວນ: total_price - discount)'); // NOT NULL
            $table->enum('buyer_type', ['student', 'teacher', 'parent', 'other'])->nullable()->comment('ປະເພດຜູ້ຊື້');
            $table->unsignedBigInteger('buyer_id')->nullable()->comment('ລະຫັດຜູ້ຊື້ (ອາດຈະແມ່ນ student_id, teacher_id, parent_id)'); // Polymorphic relation ID
            $table->timestamp('sale_date')->nullable()->useCurrent()->comment('ວັນທີ ແລະ ເວລາຂາຍ'); // Default to current time
            $table->enum('payment_method', ['cash', 'credit', 'other'])->default('cash')->comment('ວິທີການຊຳລະ'); // NOT NULL, default cash
            $table->unsignedBigInteger('sold_by')->comment('ລະຫັດຜູ້ຂາຍ/ບັນທຶກ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('item_id')
                  ->references('item_id')->on('school_store_items')
                  ->onDelete('restrict'); // Prevent deleting item if sales exist

            $table->foreign('sold_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user if they made sales

            // Note: Cannot add direct FK for buyer_id due to polymorphic nature.
            // Linking buyer needs to be handled in application logic based on buyer_type.

            // Define Indexes
            $table->index('item_id', 'IDX_StoreSales_item');
            $table->index('sold_by', 'IDX_StoreSales_seller');
            $table->index(['buyer_type', 'buyer_id'], 'IDX_StoreSales_buyer');
            $table->index('sale_date', 'IDX_StoreSales_date');
            $table->index('payment_method', 'IDX_StoreSales_payment_method');

            // Note: total_price and final_price should be calculated automatically (App logic or Trigger).
            // Note: Application logic should validate quantity > 0, prices >= 0, discount <= total_price.
            // Note: Application logic/trigger should decrease stock_quantity in school_store_items (D52) when a sale is recorded.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_sales');
    }
};
