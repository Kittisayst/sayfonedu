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
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id('student_fee_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('fee_type_id')->comment('ລະຫັດປະເພດຄ່າທຳນຽມ (FK)'); // FK to fee_types, NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->decimal('amount', 10, 2)->comment('ຈຳນວນເງິນເບື້ອງຕົ້ນ (ກ່ອນສ່ວນຫຼຸດ)'); // NOT NULL
            $table->date('due_date')->nullable()->comment('ວັນທີຄົບກຳນົດຊຳລະ');
            $table->enum('status', ['pending', 'partial', 'paid', 'waived'])->default('pending')->comment('ສະຖານະ: pending, partial, paid, waived'); // NOT NULL, default pending
            $table->decimal('discount_amount', 10, 2)->default(0.00)->comment('ຈຳນວນເງິນສ່ວນຫຼຸດ'); // NOT NULL, default 0.00
            $table->decimal('final_amount', 10, 2)->comment('ຈຳນວນເງິນສຸດທິ (amount - discount_amount)'); // NOT NULL, Should be calculated
            $table->text('description')->nullable()->comment('ໝາຍເຫດ/ລາຍລະອຽດເພີ່ມເຕີມ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('fee_type_id')
                  ->references('fee_type_id')->on('fee_types')
                  ->onDelete('restrict');

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict');

            // Define Unique Constraint
            $table->unique(['student_id', 'fee_type_id', 'academic_year_id'], 'UQ_StudFees_student_type_year');
                // ->comment('Prevent duplicate fee item (might need adjustment for recurring fees)');

            // Define Indexes
            $table->index('student_id', 'IDX_StudFees_student');
            $table->index('fee_type_id', 'IDX_StudFees_fee_type');
            $table->index('academic_year_id', 'IDX_StudFees_acad_year');
            $table->index('status', 'IDX_StudFees_status');
            $table->index('due_date', 'IDX_StudFees_due_date');

            // Note: The unique constraint might need adjustment if recurring fees (monthly, quarterly)
            // generate separate rows in this table for the same fee_type within the same year.
            // Note: final_amount should ideally be calculated automatically (e.g., using database triggers or application logic)
            // based on amount and discount_amount to ensure consistency.
            // Note: Application logic should also validate amounts (>= 0) and discount <= amount.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
