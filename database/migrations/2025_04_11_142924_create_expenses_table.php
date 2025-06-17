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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id('expense_id'); // PK
            $table->string('expense_category', 100)->comment('ໝວດໝູ່ລາຍຈ່າຍ (ເຊັ່ນ: ອຸປະກອນ, ສ້ອມແປງ)'); // NOT NULL
            $table->decimal('amount', 10, 2)->comment('ຈຳນວນເງິນທີ່ຈ່າຍ'); // NOT NULL
            $table->date('expense_date')->comment('ວັນທີທີ່ເກີດລາຍຈ່າຍ'); // NOT NULL
            $table->text('description')->nullable()->comment('ລາຍລະອຽດລາຍຈ່າຍ');
            $table->enum('payment_method', ['cash', 'bank_transfer', 'other'])->nullable()->comment('ວິທີການຈ່າຍເງິນ');
            $table->string('receipt_number', 50)->nullable()->comment('ເລກທີ່ໃບບິນ ຫຼື ເອກະສານອ້າງອີງ');
            $table->string('receipt_image', 255)->nullable()->comment('ທີ່ຢູ່ໄຟລ໌ຮູບພາບໃບບິນ (ຖ້າມີ)');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('ລະຫັດຜູ້ອະນຸມັດລາຍຈ່າຍ (FK)'); // Nullable FK to users
            $table->unsignedBigInteger('created_by')->comment('ລະຫັດຜູ້ບັນທຶກລາຍຈ່າຍ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('approved_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null'); // If approver deleted, set to NULL

            $table->foreign('created_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user who recorded expenses

            // Define Indexes
            $table->index('approved_by', 'IDX_Expenses_approver');
            $table->index('created_by', 'IDX_Expenses_creator');
            $table->index('expense_category', 'IDX_Expenses_category');
            $table->index('expense_date', 'IDX_Expenses_date');
            $table->index('payment_method', 'IDX_Expenses_payment_method');
            $table->index('receipt_number', 'IDX_Expenses_receipt_number');

            // Note: Application logic should validate amount > 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
