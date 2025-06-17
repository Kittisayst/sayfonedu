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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id('document_id');
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK ຈາກ Students)');
            $table->string('document_type', 100)->comment('ປະເພດເອກະສານ (ເຊັ່ນ: ໃບແຈ້ງໂທດ, ໃບຄະແນນ, ສຳມະໂນຄົວ, ບັດປະຈຳຕົວ)');
            $table->string('document_name', 255)->comment('ຊື່ເອກະສານ ຫຼື ຊື່ໄຟລ໌');
            $table->string('file_path', 255)->comment('ທີ່ຢູ່ເກັບໄຟລ໌ໃນລະບົບ');
            $table->integer('file_size')->nullable()->comment('ຂະໜາດໄຟລ໌ (ເປັນ bytes)');
            $table->string('file_type', 50)->nullable()->comment('ຊະນິດຂອງໄຟລ໌ (MIME Type ຫຼື ນາມສະກຸນ, ເຊັ່ນ: application/pdf, image/jpeg)');
            $table->timestamp('upload_date')->nullable()->default(now())->comment('ວັນທີ ແລະ ເວລາອັບໂຫຼດ');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍເພີ່ມເຕີມກ່ຽວກັບເອກະສານ');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            
            // Indexes
            $table->index(['student_id'], 'IDX_StudDocs_student');
            $table->index(['document_type'], 'IDX_StudDocs_type');
            $table->index(['upload_date'], 'IDX_StudDocs_upload_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
