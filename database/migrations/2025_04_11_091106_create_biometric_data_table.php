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
        Schema::create('biometric_data', function (Blueprint $table) {
            $table->id('biometric_id');
            $table->unsignedBigInteger('user_id')->comment('ລະຫັດຜູ້ໃຊ້ເຈົ້າຂອງຂໍ້ມູນ (FK ຈາກ Users)');
            $table->enum('biometric_type', ['fingerprint', 'face'])->comment('ປະເພດຂໍ້ມູນຊີວະມິຕິ: fingerprint, face');
            $table->longText('biometric_data')->comment('ຂໍ້ມູນຊີວະມິຕິຕົວຈິງ (template/binary data)');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('ສະຖານະຂໍ້ມູນນີ້: active, inactive');
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->index(['user_id'], 'IDX_BiometricData_user');
            $table->index(['biometric_type'], 'IDX_BiometricData_type');
            $table->index(['status'], 'IDX_BiometricData_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_data');
    }
};
