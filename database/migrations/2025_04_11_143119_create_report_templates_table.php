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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id('template_id'); // PK
            $table->string('template_name', 100)->unique()->comment('ຊື່ແມ່ແບບລາຍງານ'); // Unique, NOT NULL
            $table->string('template_type', 50)->nullable()->comment('ປະເພດຂອງແມ່ແບບ (ຕົວຢ່າງ: Transcript, Attendance)');
            $table->longText('template_content')->nullable()->comment('ເນື້ອຫາ/ໂຄງສ້າງຂອງແມ່ແບບ (HTML, XML, etc.)');
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE = ໃຊ້ງານໄດ້)'); // NOT NULL, default true
            $table->unsignedBigInteger('created_by')->comment('ລະຫັດຜູ້ສ້າງ/ອັບໂຫຼດ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('created_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user who created templates

            // Unique constraint on template_name defined inline

            // Define Indexes
            $table->index('created_by', 'IDX_ReportTemplates_creator');
            $table->index('template_type', 'IDX_ReportTemplates_type');
            $table->index('is_active', 'IDX_ReportTemplates_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};
