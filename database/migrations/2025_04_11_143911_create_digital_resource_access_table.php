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
        Schema::create('digital_resource_access', function (Blueprint $table) {
            $table->id('access_id'); // PK
            $table->unsignedBigInteger('resource_id')->comment('ລະຫັດຊັບພະຍາກອນທີ່ເຂົ້າເຖິງ (FK)'); // FK to digital_library_resources, NOT NULL
            $table->unsignedBigInteger('user_id')->comment('ລະຫັດຜູ້ໃຊ້ທີ່ເຂົ້າເຖິງ (FK)'); // FK to users, NOT NULL
            $table->timestamp('access_time')->nullable()->useCurrent()->comment('ວັນທີ ແລະ ເວລາທີ່ເຂົ້າເຖິງ'); // Default to current time
            $table->enum('access_type', ['view', 'download', 'print'])->comment('ປະເພດການເຂົ້າເຖິງ'); // NOT NULL
            $table->string('device_info', 255)->nullable()->comment('ຂໍ້ມູນອຸປະກອນ (ເຊັ່ນ: User Agent)');
            $table->ipAddress('ip_address')->nullable()->comment('ທີ່ຢູ່ IP ຂອງຜູ້ໃຊ້'); // Use specific ipAddress type
            // No standard timestamps() as updated_at is not needed for logs
            $table->timestamp('created_at')->nullable()->useCurrent(); // Explicitly add created_at

            // Define Foreign Keys
            $table->foreign('resource_id')
                  ->references('resource_id')->on('digital_library_resources')
                  ->onDelete('cascade'); // If resource deleted, delete access logs

            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // If user deleted, delete their access logs

            // Define Indexes
            $table->index('resource_id', 'IDX_DigResAccess_resource');
            $table->index('user_id', 'IDX_DigResAccess_user');
            $table->index('access_time', 'IDX_DigResAccess_time');
            $table->index('access_type', 'IDX_DigResAccess_type');
            $table->index('ip_address', 'IDX_DigResAccess_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_resource_access');
    }
};
