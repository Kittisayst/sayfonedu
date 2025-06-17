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
        Schema::create('teacher_documents', function (Blueprint $table) {
            $table->id('document_id'); // PK
            $table->unsignedBigInteger('teacher_id')->comment('ລະຫັດຄູສອນ (FK)'); // FK to teachers
            $table->string('document_type', 100)->comment('ປະເພດເອກະສານ (ເຊັ່ນ: ໃບປະກາດ, ສັນຍາຈ້າງ)'); // NOT NULL
            $table->string('document_name', 255)->comment('ຊື່ເອກະສານ/ຊື່ໄຟລ໌'); // NOT NULL
            $table->string('file_path', 255)->comment('ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ'); // NOT NULL
            $table->integer('file_size')->nullable()->comment('ຂະໜາດໄຟລ໌ (ເປັນ bytes)');
            $table->string('file_type', 100)->nullable()->comment('ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ)');
            $table->timestamp('upload_date')->nullable()->useCurrent()->comment('ວັນທີ ແລະ ເວລາອັບໂຫຼດ'); // Default to current time
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('teacher_id')
                  ->references('teacher_id')->on('teachers')
                  ->onDelete('cascade'); // If teacher deleted, delete their documents

            // Define Indexes
            $table->index('teacher_id', 'IDX_TeacherDocs_teacher');
            $table->index('document_type', 'IDX_TeacherDocs_type');
            $table->index('upload_date', 'IDX_TeacherDocs_upload_date');

            // Note: Consider adding a unique constraint if needed,
            // e.g., unique(['teacher_id', 'document_name']) or unique(['teacher_id', 'document_type'])
            // depending on requirements.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_documents');
    }
};
