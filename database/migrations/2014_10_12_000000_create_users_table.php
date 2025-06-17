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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username', 50)->unique()->comment('ຊື່ຜູ້ໃຊ້ສຳລັບເຂົ້າລະບົບ');
            $table->string('password', 255)->comment('ລະຫັດຜ່ານ (ເກັບແບບເຂົ້າລະຫັດ)');
            $table->string('email', 100)->unique()->comment('ອີເມວ');
            $table->string('phone', 20)->nullable()->unique()->comment('ເບີໂທລະສັບ');
            $table->unsignedBigInteger('role_id')->comment('ລະຫັດບົດບາດ (FK)');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->comment('ສະຖານະຜູ້ໃຊ້: active, inactive, suspended');
            $table->string('profile_image', 255)->nullable()->comment('ທີ່ຢູ່ຮູບພາບໂປຣໄຟລ໌');
            $table->timestamp('last_login')->nullable()->comment('ເວລາເຂົ້າລະບົບຄັ້ງສຸດທ້າຍ');
            $table->timestamps();
            
            $table->foreign('role_id')->references('role_id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
