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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id'); // PK
            $table->unsignedBigInteger('user_id')->comment('ລະຫັດຜູ້ໃຊ້ທີ່ໄດ້ຮັບການແຈ້ງເຕືອນ (FK)'); // FK to users, NOT NULL
            $table->string('title', 255)->comment('ຫົວຂໍ້ການແຈ້ງເຕືອນ'); // NOT NULL
            $table->text('content')->nullable()->comment('ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງການແຈ້ງເຕືອນ');
            $table->string('notification_type', 50)->nullable()->comment('ປະເພດຂອງການແຈ້ງເຕືອນ (ຕົວຢ່າງ: new_message, request_approved)');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID ຂອງຂໍ້ມູນທີ່ກ່ຽວຂ້ອງ (ເຊັ່ນ: message_id, request_id)'); // Polymorphic relation ID
            $table->boolean('is_read')->default(false)->comment('ສະຖານະການອ່ານ (TRUE = ອ່ານແລ້ວ)'); // NOT NULL, default false
            $table->timestamp('read_at')->nullable()->comment('ວັນທີ ແລະ ເວລາທີ່ອ່ານ (NULL = ຍັງບໍ່ອ່ານ)');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key for user_id
            $table->foreign('user_id')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade'); // If user deleted, delete their notifications

            // Note: We cannot add a database-level foreign key constraint on 'related_id'
            // because it can relate to different tables based on 'notification_type'.
            // This type of relationship (Polymorphic) is handled in the application logic (Eloquent Models).

            // Define Indexes
            $table->index('user_id', 'IDX_Notifications_user');
            $table->index(['user_id', 'is_read'], 'IDX_Notifications_user_read'); // For querying user's notifications (especially unread)
            $table->index(['notification_type', 'related_id'], 'IDX_Notifications_related'); // For finding notifications related to a specific item
            $table->index('created_at', 'IDX_Notifications_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
