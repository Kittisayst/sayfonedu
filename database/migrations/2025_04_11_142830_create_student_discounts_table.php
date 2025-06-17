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
        Schema::create('student_discounts', function (Blueprint $table) {
            $table->id('student_discount_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to students, NOT NULL
            $table->unsignedBigInteger('discount_id')->comment('ລະຫັດປະເພດສ່ວນຫຼຸດ (FK)'); // FK to discounts, NOT NULL
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->date('start_date')->nullable()->comment('ວັນທີທີ່ສ່ວນຫຼຸດເລີ່ມມີຜົນ');
            $table->date('end_date')->nullable()->comment('ວັນທີທີ່ສ່ວນຫຼຸດໝົດຜົນ (NULL = ບໍ່ມີກຳນົດ)');
            $table->text('reason')->nullable()->comment('ເຫດຜົນ ຫຼື ທີ່ມາຂອງການໄດ້ຮັບສ່ວນຫຼຸດ');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('ລະຫັດຜູ້ອະນຸມັດ (FK)'); // FK to users, Nullable
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('ສະຖານະການນຳໃຊ້: active, inactive'); // NOT NULL, default active
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade');

            $table->foreign('discount_id')
                  ->references('discount_id')->on('discounts')
                  ->onDelete('restrict');

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict');

            $table->foreign('approved_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null');

            // Define Unique Constraint
            $table->unique(['student_id', 'discount_id', 'academic_year_id'], 'UQ_StudDisc_student_discount_year');
                // ->comment('Prevent assigning the same discount type twice to the same student in the same year');

            // Define Indexes
            $table->index('student_id', 'IDX_StudDisc_student');
            $table->index('discount_id', 'IDX_StudDisc_discount');
            $table->index('academic_year_id', 'IDX_StudDisc_acad_year');
            $table->index('approved_by', 'IDX_StudDisc_approver');
            $table->index('status', 'IDX_StudDisc_status');
            $table->index(['start_date', 'end_date'], 'IDX_StudDisc_dates');

            // Note: UQ constraint might need adjustment based on specific school policies
            // (e.g., if a student can receive the same type of scholarship multiple times per year).
            // Note: Application logic should validate date sequence (end_date >= start_date) if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_discounts');
    }
};
