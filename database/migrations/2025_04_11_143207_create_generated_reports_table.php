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
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id('report_id'); // PK
            $table->string('report_name', 255)->comment('ຊື່ລາຍງານ (ທີ່ຜູ້ໃຊ້ຕັ້ງ ຫຼື ລະບົບສ້າງ)'); // NOT NULL
            $table->unsignedBigInteger('template_id')->comment('ລະຫັດແມ່ແບບທີ່ໃຊ້ (FK)'); // FK to report_templates, NOT NULL
            $table->string('report_type', 50)->nullable()->comment('ປະເພດຂອງລາຍງານ (ຄວນກົງກັບແມ່ແບບ)');
            $table->longText('report_data')->nullable()->comment('ຂໍ້ມູນດິບທີ່ໃຊ້ສ້າງ (JSON, XML, etc.) - ອາດຈະບໍ່ເກັບ');
            $table->enum('report_format', ['pdf', 'excel', 'word', 'html'])->comment('ຮູບແບບຜົນລັບ'); // NOT NULL
            $table->string('file_path', 255)->nullable()->comment('ທີ່ຢູ່ເກັບໄຟລ໌ລາຍງານ (ຖ້າບັນທຶກເປັນໄຟລ໌)');
            $table->unsignedBigInteger('generated_by')->comment('ລະຫັດຜູ້ສ້າງລາຍງານ (FK)'); // FK to users, NOT NULL
            $table->timestamp('generated_at')->nullable()->useCurrent()->comment('ວັນທີ ແລະ ເວລາທີ່ສ້າງລາຍງານ'); // Specific generation timestamp
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('template_id')
                  ->references('template_id')->on('report_templates')
                  ->onDelete('restrict'); // Prevent deleting template if reports exist

            $table->foreign('generated_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user if they generated reports

            // Define Indexes
            $table->index('template_id', 'IDX_GenReports_template');
            $table->index('generated_by', 'IDX_GenReports_generator');
            $table->index('report_name', 'IDX_GenReports_name');
            $table->index('report_type', 'IDX_GenReports_type');
            $table->index('report_format', 'IDX_GenReports_format');
            $table->index('generated_at', 'IDX_GenReports_generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
