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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id('log_id'); // PK
            $table->enum('log_level', ['info', 'warning', 'error', 'critical'])->comment('ລະດັບ Log'); // NOT NULL
            $table->string('log_source', 100)->nullable()->comment('ແຫຼ່ງທີ່ມາຂອງ Log (ເຊັ່ນ: Module, Function)');
            $table->text('message')->comment('ຂໍ້ຄວາມ Log'); // NOT NULL
            $table->text('context')->nullable()->comment('ຂໍ້ມູນເພີ່ມເຕີມ (Context) ເຊັ່ນ: Stack trace, JSON');
            $table->ipAddress('ip_address')->nullable()->comment('ທີ່ຢູ່ IP ທີ່ກ່ຽວຂ້ອງ (ຖ້າມີ)');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ລະຫັດຜູ້ໃຊ້ທີ່ກ່ຽວຂ້ອງ (FK)'); // FK to users, Nullable
            // Only created_at is needed for logs, not updated_at
            $table->timestamp('created_at')->nullable()->useCurrent(); // Use current timestamp as default

            // Define Foreign Key (Optional, if user_id links to users)
            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('set null'); // If user deleted, keep log but set user_id to NULL

            // Define Indexes
            $table->index('log_level', 'IDX_SystemLogs_level');
            $table->index('log_source', 'IDX_SystemLogs_source');
            $table->index('user_id', 'IDX_SystemLogs_user');
            $table->index('ip_address', 'IDX_SystemLogs_ip');
            $table->index('created_at', 'IDX_SystemLogs_created_at'); // Important for querying logs by time
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
