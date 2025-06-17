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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id('academic_year_id'); // PK
            $table->string('year_name', 50)->unique()->comment('ຊື່/ປີຂອງສົກຮຽນ (ຕົວຢ່າງ: 2024-2025)'); // Unique, NOT NULL
            $table->date('start_date')->comment('ວັນທີເລີ່ມຕົ້ນສົກຮຽນ'); // NOT NULL
            $table->date('end_date')->comment('ວັນທີສິ້ນສຸດສົກຮຽນ'); // NOT NULL
            $table->boolean('is_current')->default(false)->comment('ກຳນົດວ່າແມ່ນສົກຮຽນປັດຈຸບັນ ຫຼື ບໍ່'); // NOT NULL, default false
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming')->comment('ສະຖານະຂອງສົກຮຽນ'); // NOT NULL, default upcoming
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined in this table

            // Unique constraint on year_name already defined inline

            // Indexes for performance
            $table->index('is_current', 'IDX_AcademicYears_current');
            $table->index('status', 'IDX_AcademicYears_status');
            $table->index(['start_date', 'end_date'], 'IDX_AcademicYears_dates');

            // Note: Application logic should ensure end_date > start_date.
            // Note: Logic should ensure only one academic year has is_current = TRUE at any time.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
