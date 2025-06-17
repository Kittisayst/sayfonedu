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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id('subject_id'); // PK
            $table->string('subject_code', 20)->unique()->comment('ລະຫັດວິຊາ (ຕົວຢ່າງ: MTH101)'); // Unique, NOT NULL
            $table->string('subject_name_lao', 100)->unique()->comment('ຊື່ວິຊາ (ພາສາລາວ)'); // Unique, NOT NULL
            $table->string('subject_name_en', 100)->nullable()->unique()->comment('ຊື່ວິຊາ (ພາສາອັງກິດ)'); // Nullable, Unique
            $table->integer('credit_hours')->nullable()->comment('ຈຳນວນໜ່ວຍກິດ (ຖ້າມີ)');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍກ່ຽວກັບວິຊາ');
            $table->string('category', 50)->nullable()->comment('ໝວດໝູ່ຂອງວິຊາ (ຕົວຢ່າງ: ວິທະຍາສາດ)');
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE = ຍັງເປີດສອນ)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // No Foreign Keys defined for this table

            // Unique constraints defined inline with columns

            // Indexes for performance
            $table->index('category', 'IDX_Subjects_category');
            $table->index('is_active', 'IDX_Subjects_active');

            // Note: Application logic should validate credit_hours >= 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
