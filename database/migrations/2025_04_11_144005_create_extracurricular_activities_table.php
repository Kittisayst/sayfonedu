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
        Schema::create('extracurricular_activities', function (Blueprint $table) {
            $table->id('activity_id'); // PK
            $table->string('activity_name', 255)->comment('ຊື່ກິດຈະກຳ'); // NOT NULL
            $table->string('activity_type', 100)->nullable()->comment('ປະເພດກິດຈະກຳ (ເຊັ່ນ: ຊົມລົມ, ກິລາ)');
            $table->text('description')->nullable()->comment('ລາຍລະອຽດກິດຈະກຳ');
            $table->date('start_date')->nullable()->comment('ວັນທີເລີ່ມກິດຈະກຳ');
            $table->date('end_date')->nullable()->comment('ວັນທີສິ້ນສຸດກິດຈະກຳ');
            $table->string('schedule', 255)->nullable()->comment('ຕາຕະລາງເວລາ (ແບບຂໍ້ຄວາມ)');
            $table->string('location', 255)->nullable()->comment('ສະຖານທີ່ຈັດກິດຈະກຳ');
            $table->integer('max_participants')->nullable()->comment('ຈຳນວນຜູ້ເຂົ້າຮ່ວມສູງສຸດ (NULL=ບໍ່ຈຳກັດ)');
            $table->unsignedBigInteger('coordinator_id')->nullable()->comment('ລະຫັດຜູ້ປະສານງານ/ຮັບຜິດຊອບ (FK)'); // FK to users, Nullable
            $table->unsignedBigInteger('academic_year_id')->comment('ລະຫັດສົກຮຽນ (FK)'); // FK to academic_years, NOT NULL
            $table->boolean('is_active')->default(true)->comment('ສະຖານະ (TRUE=ເປີດຮັບ/ດຳເນີນຢູ່)'); // NOT NULL, default true
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Keys
            $table->foreign('coordinator_id')
                  ->references('user_id')->on('users')
                  ->onDelete('set null'); // If coordinator deleted, keep activity

            $table->foreign('academic_year_id')
                  ->references('academic_year_id')->on('academic_years')
                  ->onDelete('restrict'); // Prevent deleting academic year if activities exist

            // Define Unique Constraint
            $table->unique(['academic_year_id', 'activity_name'], 'UQ_ExtraAct_name_year');
                // ->comment('Activity name must be unique within an academic year');

            // Define Indexes
            $table->index('coordinator_id', 'IDX_ExtraAct_coord');
            $table->index('academic_year_id', 'IDX_ExtraAct_acad_year');
            $table->index('activity_type', 'IDX_ExtraAct_type');
            $table->index('is_active', 'IDX_ExtraAct_active');
            $table->index(['start_date', 'end_date'], 'IDX_ExtraAct_dates');

            // Note: Application logic should validate date sequence (end_date >= start_date)
            // and max_participants (if not null) >= 0 if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extracurricular_activities');
    }
};
