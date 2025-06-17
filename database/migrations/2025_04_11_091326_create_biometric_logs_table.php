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
        Schema::create('biometric_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('user_id')->comment('ລະຫັດຜູ້ໃຊ້ທີ່ພະຍາຍາມສະແກນ (FK ຈາກ Users)');
            $table->unsignedBigInteger('biometric_id')->nullable()->comment('ລະຫັດຂໍ້ມູນຊີວະມິຕິທີ່ໃຊ້ (FK ຈາກ Biometric_Data). ອາດຈະ NULL ຖ້າສະແກນບໍ່ຜ່ານ ຫຼື ຫາ User ບໍ່ພົບ.');
            $table->enum('log_type', ['check_in', 'check_out', 'authentication'])->comment('ປະເພດການໃຊ້ງານ: check_in, check_out, authentication');
            $table->enum('status', ['success', 'failed'])->comment('ຜົນລັບການສະແກນ: success, failed');
            $table->string('device_id', 100)->nullable()->comment('ລະຫັດເຄື່ອງສະແກນ/ອຸປະກອນທີ່ໃຊ້');
            $table->string('location', 100)->nullable()->comment('ສະຖານທີ່ຕິດຕັ້ງເຄື່ອງສະແກນ (ເຊັ່ນ: ປະຕູໜ້າ, ຫ້ອງການ)');
            $table->timestamp('log_time')->nullable()->default(now())->comment('ເວລາທີ່ເກີດເຫດການສະແກນ');
            $table->timestamp('created_at')->default(now())->comment('ເວລາທີ່ບັນທຶກ Log ນີ້');
            
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('biometric_id')->references('biometric_id')->on('biometric_data');
            
            $table->index(['user_id'], 'IDX_BiometricLogs_user');
            $table->index(['biometric_id'], 'IDX_BiometricLogs_bio_data');
            $table->index(['log_type'], 'IDX_BiometricLogs_log_type');
            $table->index(['status'], 'IDX_BiometricLogs_status');
            $table->index(['log_time'], 'IDX_BiometricLogs_log_time');
            $table->index(['device_id'], 'IDX_BiometricLogs_device');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_logs');
    }
};
