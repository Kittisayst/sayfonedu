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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id'); // PK
            $table->string('title', 255)->comment('ຫົວຂໍ້ປະກາດ'); // NOT NULL
            $table->text('content')->nullable()->comment('ເນື້ອໃນ ຫຼື ລາຍລະອຽດຂອງປະກາດ');
            $table->date('start_date')->nullable()->comment('ວັນທີເລີ່ມສະແດງປະກາດນີ້');
            $table->date('end_date')->nullable()->comment('ວັນທີສິ້ນສຸດການສະແດງປະກາດ (NULL = ບໍ່ມີກຳນົດ)');
            $table->enum('target_group', ['all', 'teachers', 'students', 'parents'])->default('all')->comment('ກຸ່ມເປົ້າໝາຍທີ່ເຫັນປະກາດ'); // NOT NULL, default 'all'
            $table->boolean('is_pinned')->default(false)->comment('ປັກໝຸດປະກາດນີ້ໄວ້ເທິງສຸດ ຫຼື ບໍ່'); // NOT NULL, default false
            $table->string('attachment', 255)->nullable()->comment('ທີ່ຢູ່ຂອງໄຟລ໌ແນບ (ຖ້າມີ)');
            $table->unsignedBigInteger('created_by')->comment('ລະຫັດຜູ້ສ້າງປະກາດ (FK)'); // FK to users, NOT NULL
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('created_by')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict'); // Prevent deleting user who created announcements

            // Define Indexes
            $table->index('created_by', 'IDX_Announcements_creator');
            $table->index('target_group', 'IDX_Announcements_target');
            $table->index(['start_date', 'end_date'], 'IDX_Announcements_dates');
            $table->index('is_pinned', 'IDX_Announcements_pinned');
            $table->index('created_at', 'IDX_Announcements_created_at');

            // Note: Application logic should validate date sequence (end_date >= start_date) if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
