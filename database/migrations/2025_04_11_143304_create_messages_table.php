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
        Schema::create('messages', function (Blueprint $table) {
            $table->id('message_id'); // PK
            $table->unsignedBigInteger('sender_id')->comment('ລະຫັດຜູ້ສົ່ງ (FK)'); // FK to users, NOT NULL
            $table->unsignedBigInteger('receiver_id')->comment('ລະຫັດຜູ້ຮັບ (FK)'); // FK to users, NOT NULL
            $table->string('subject', 255)->nullable()->comment('ຫົວຂໍ້ຂໍ້ຄວາມ');
            $table->text('message_content')->nullable()->comment('ເນື້ອໃນຂໍ້ຄວາມ');
            $table->boolean('read_status')->default(false)->comment('ສະຖານະການອ່ານ (TRUE=ອ່ານແລ້ວ)'); // NOT NULL, default false
            $table->timestamp('read_at')->nullable()->comment('ເວລາທີ່ອ່ານ (NULL=ຍັງບໍ່ອ່ານ)');
            $table->string('attachment', 255)->nullable()->comment('ທີ່ຢູ່ໄຟລ໌ແນບ (ຖ້າມີ)');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('sender_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // Or SET NULL depending on policy

            $table->foreign('receiver_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // Or SET NULL depending on policy

            // Define Indexes
            $table->index('sender_id', 'IDX_Messages_sender');
            $table->index('receiver_id', 'IDX_Messages_receiver');
            $table->index(['receiver_id', 'read_status'], 'IDX_Messages_receiver_read'); // For inbox performance
            $table->index('created_at', 'IDX_Messages_created_at');

            // Note: Application logic could prevent sender_id = receiver_id if needed.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
