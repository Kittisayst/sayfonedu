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
        Schema::create('digital_library_resources', function (Blueprint $table) {
            $table->id('resource_id'); // PK
            $table->string('title', 255)->comment('ຊື່ ຫຼື ຫົວຂໍ້ຂອງຊັບພະຍາກອນ'); // NOT NULL
            $table->string('author', 255)->nullable()->comment('ຊື່ຜູ້ແຕ່ງ ຫຼື ຜູ້ສ້າງ');
            $table->string('publisher', 255)->nullable()->comment('ຊື່ສຳນັກພິມ ຫຼື ຜູ້ເຜີຍແຜ່');
            $table->year('publication_year')->nullable()->comment('ປີທີ່ພິມ ຫຼື ເຜີຍແຜ່ (ຄ.ສ.)'); // Use year type
            $table->enum('resource_type', ['book', 'document', 'video', 'audio', 'image'])->comment('ປະເພດຊັບພະຍາກອນ'); // NOT NULL
            $table->string('category', 100)->nullable()->comment('ໝວດໝູ່ (ຕົວຢ່າງ: Science, History)');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍ ຫຼື ເນື້ອຫຍໍ້');
            $table->string('file_path', 255)->comment('ທີ່ຢູ່ຂອງໄຟລ໌ຊັບພະຍາກອນຕົວຈິງ'); // NOT NULL
            $table->integer('file_size')->nullable()->comment('ຂະໜາດຂອງໄຟລ໌ (ເປັນ bytes)');
            $table->string('thumbnail', 255)->nullable()->comment('ທີ່ຢູ່ຂອງໄຟລ໌ຮູບຕົວຢ່າງ/ໜ້າປົກ');
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE = ສາມາດເຂົ້າເຖິງໄດ້)'); // NOT NULL, default true
            $table->unsignedBigInteger('added_by')->comment('ລະຫັດຜູ້ໃຊ້ທີ່ເພີ່ມຊັບພະຍາກອນນີ້ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('added_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user if they added resources

            // Define Indexes
            $table->index('added_by', 'IDX_DigLibRes_adder');
            $table->index('title', 'IDX_DigLibRes_title');
            $table->index('author', 'IDX_DigLibRes_author');
            $table->index('resource_type', 'IDX_DigLibRes_type');
            $table->index('category', 'IDX_DigLibRes_category');
            $table->index('is_active', 'IDX_DigLibRes_active');

            // Note: Application logic should validate publication_year and file_size if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_library_resources');
    }
};
