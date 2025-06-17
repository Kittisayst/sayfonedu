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
        Schema::create('income', function (Blueprint $table) {
            $table->id('income_id'); // PK
            $table->string('income_category', 100)->comment('ໝວດໝູ່ລາຍຮັບ (ຕົວຢ່າງ: ເງິນບໍລິຈາກ)'); // NOT NULL
            $table->decimal('amount', 10, 2)->comment('ຈຳນວນເງິນທີ່ໄດ້ຮັບ'); // NOT NULL
            $table->date('income_date')->comment('ວັນທີທີ່ໄດ້ຮັບລາຍຮັບ'); // NOT NULL
            $table->text('description')->nullable()->comment('ລາຍລະອຽດ ຫຼື ແຫຼ່ງທີ່ມາ');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qr_code', 'other'])->nullable()->comment('ວິທີການຮັບເງິນ');
            $table->string('receipt_number', 50)->nullable()->comment('ເລກທີ່ໃບຮັບເງິນ ຫຼື ເອກະສານອ້າງອີງ');
            $table->unsignedBigInteger('received_by')->comment('ລະຫັດຜູ້ຮັບເງິນ/ບັນທຶກ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('received_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user if they received income

            // Define Indexes
            $table->index('received_by', 'IDX_Income_receiver');
            $table->index('income_category', 'IDX_Income_category');
            $table->index('income_date', 'IDX_Income_date');
            $table->index('payment_method', 'IDX_Income_payment_method');
            $table->index('receipt_number', 'IDX_Income_receipt_number');

            // Note: Application logic should validate amount > 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income');
    }
};
