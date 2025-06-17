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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // PK
            $table->unsignedBigInteger('student_fee_id')->comment('ລະຫັດລາຍການຄ່າທຳນຽມທີ່ຊຳລະ (FK)'); // FK to student_fees, NOT NULL
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL (Redundant but can be useful)
            $table->decimal('amount', 10, 2)->comment('ຈຳນວນເງິນທີ່ຊຳລະ'); // NOT NULL
            $table->date('payment_date')->comment('ວັນທີຊຳລະ'); // NOT NULL
            $table->enum('payment_method', ['cash', 'bank_transfer', 'qr_code', 'other'])->comment('ວິທີການຊຳລະ'); // NOT NULL
            $table->string('transaction_id', 100)->nullable()->comment('ລະຫັດອ້າງອີງທຸລະກຳ (ຖ້າໂອນ/QR)');
            $table->string('receipt_number', 50)->nullable()->comment('ເລກທີ່ໃບຮັບເງິນ (ຂອງໂຮງຮຽນ)');
            $table->text('payment_note')->nullable()->comment('ໝາຍເຫດກ່ຽວກັບການຊຳລະ');
            $table->unsignedBigInteger('received_by')->comment('ລະຫັດຜູ້ຮັບເງິນ/ບັນທຶກ (FK)'); // FK to users, NOT NULL
            $table->boolean('is_confirmed')->default(true)->comment('ຢືນຢັນການໄດ້ຮັບເງິນແລ້ວ'); // NOT NULL, Default needs review based on workflow
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_fee_id')
                  ->references('student_fee_id')->on('student_fees')
                  ->onDelete('restrict'); // Prevent deleting fee item if payments exist

            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('restrict'); // Prevent deleting student if payments exist

            $table->foreign('received_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user if they received payments

            // Define Indexes
            $table->index('student_fee_id', 'IDX_Payments_stud_fee');
            $table->index('student_id', 'IDX_Payments_student');
            $table->index('received_by', 'IDX_Payments_receiver');
            $table->index('payment_date', 'IDX_Payments_date');
            $table->index('payment_method', 'IDX_Payments_method');
            $table->index('receipt_number', 'IDX_Payments_receipt');
            $table->index('transaction_id', 'IDX_Payments_transaction');
            $table->index('is_confirmed', 'IDX_Payments_confirmed');

            // Note: Application logic should validate amount > 0.
            // Note: Default for is_confirmed might need adjustment based on process (e.g., default to FALSE for bank transfers).
            // Note: Ensure student_id here matches the one linked via student_fee_id in application logic.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
