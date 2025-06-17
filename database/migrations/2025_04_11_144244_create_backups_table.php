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
        Schema::create('backups', function (Blueprint $table) {
            $table->id('backup_id'); // PK
            $table->string('backup_name', 255)->unique()->comment('ຊື່ໄຟລ໌ ຫຼື ຊື່ການສຳຮອງຂໍ້ມູນ'); // Unique, NOT NULL
            $table->enum('backup_type', ['full', 'partial'])->comment('ປະເພດການສຳຮອງຂໍ້ມູນ'); // NOT NULL
            $table->string('file_path', 255)->comment('ທີ່ຢູ່ເກັບໄຟລ໌ backup'); // NOT NULL
            $table->unsignedBigInteger('file_size')->nullable()->comment('ຂະໜາດໄຟລ໌ (bytes)'); // Use unsignedBigInteger for large sizes, Nullable
            $table->timestamp('backup_date')->nullable()->useCurrent()->comment('ວັນທີ ແລະ ເວລາສຳຮອງຂໍ້ມູນ'); // Default to current time
            $table->enum('status', ['success', 'failed', 'in_progress'])->default('in_progress')->comment('ສະຖານະ'); // NOT NULL, default 'in_progress'
            $table->unsignedBigInteger('initiated_by')->nullable()->comment('ລະຫັດຜູ້ເລີ່ມດຳເນີນການ (FK)'); // FK to users, Nullable
            $table->text('description')->nullable()->comment('ໝາຍເຫດເພີ່ມເຕີມ');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key (Optional, if initiated_by links to users)
            $table->foreign('initiated_by')
                  ->references('user_id')->on('users')
                  ->onDelete('set null'); // If initiator user deleted, set to NULL

            // Unique constraint on backup_name defined inline

            // Define Indexes
            $table->index('initiated_by', 'IDX_Backups_initiator');
            $table->index('backup_type', 'IDX_Backups_type');
            $table->index('backup_date', 'IDX_Backups_date');
            $table->index('status', 'IDX_Backups_status');

            // Note: Application logic should validate file_size >= 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
