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
        Schema::create('requests', function (Blueprint $table) {
            $table->id('request_id'); // PK
            $table->unsignedBigInteger('user_id')->comment('ລະຫັດຜູ້ຍື່ນຄຳຮ້ອງ (FK)'); // FK to users, NOT NULL
            $table->string('request_type', 100)->comment('ປະເພດຄຳຮ້ອງ (ຕົວຢ່າງ: Document Request)'); // NOT NULL
            $table->string('subject', 255)->comment('ຫົວຂໍ້ຂອງຄຳຮ້ອງ'); // NOT NULL
            $table->text('content')->nullable()->comment('ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງຄຳຮ້ອງ');
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing'])->default('pending')->comment('ສະຖານະ: pending, approved, rejected, processing'); // NOT NULL, default pending
            $table->text('response')->nullable()->comment('ຄຳຕອບ ຫຼື ຜົນການດຳເນີນການ');
            $table->string('attachment', 255)->nullable()->comment('ທີ່ຢູ່ໄຟລ໌ແນບທີ່ຜູ້ຮ້ອງສົ່ງມາ (ຖ້າມີ)');
            $table->unsignedBigInteger('handled_by')->nullable()->comment('ລະຫັດຜູ້ດຳເນີນການ/ອະນຸມັດ (FK)'); // FK to users, Nullable
            $table->timestamp('handled_at')->nullable()->comment('ວັນທີ ແລະ ເວລາທີ່ດຳເນີນການສຳເລັດ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // If user deleted, delete their requests

            $table->foreign('handled_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null'); // If handler deleted, keep request but set handler to NULL

            // Define Indexes
            $table->index('user_id', 'IDX_Requests_user');
            $table->index('handled_by', 'IDX_Requests_handler');
            $table->index('request_type', 'IDX_Requests_type');
            $table->index('status', 'IDX_Requests_status');
            $table->index('created_at', 'IDX_Requests_created_at');
            $table->index('handled_at', 'IDX_Requests_handled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
