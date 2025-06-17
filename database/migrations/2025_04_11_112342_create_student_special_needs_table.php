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
        Schema::create('student_special_needs', function (Blueprint $table) {
            $table->id('special_need_id'); // PK
            $table->unsignedBigInteger('student_id')->comment('ລະຫັດນັກຮຽນ (FK)'); // FK to Students
            $table->string('need_type', 100)->comment('ປະເພດຄວາມຕ້ອງການພິເສດ (ເຊັ່ນ: ด้านการเรียนรู้, ด้านร่างกาย)'); // NOT NULL
            $table->text('description')->comment('ລາຍລະອຽດຂອງຄວາມຕ້ອງການ'); // NOT NULL
            $table->text('recommendations')->nullable()->comment('ຂໍ້ສະເໜີແນະໃນການຊ່ວຍເຫຼືອ/ຈັດການຮຽນ');
            $table->text('support_required')->nullable()->comment('ການສະໜັບສະໜູນທີ່ຕ້ອງການຈາກໂຮງຮຽນ');
            $table->string('external_support', 255)->nullable()->comment('ຂໍ້ມູນການສະໜັບສະໜູນຈາກພາຍນອກ (ຖ້າມີ)');
            $table->date('start_date')->nullable()->comment('ວັນທີເລີ່ມຕົ້ນ (ທີ່ພົບ ຫຼື ເລີ່ມຊ່ວຍເຫຼືອ)');
            $table->date('end_date')->nullable()->comment('ວັນທີສິ້ນສຸດ (ຖ້າມີ)');
            $table->timestamps(); // created_at and updated_at

            // Define Foreign Key
            $table->foreign('student_id')
                  ->references('student_id')->on('students')
                  ->onDelete('cascade'); // If student deleted, delete special needs record

            // Define Unique Constraint
            $table->unique(['student_id', 'need_type'], 'UQ_StudSpecialNeeds_student_type');
                // ->comment('ນັກຮຽນໜຶ່ງຄົນຄວນມີບັນທຶກຄວາມຕ້ອງການປະເພດດຽວກັນພຽງອັນດຽວ'); // Comment for unique constraint

            // Define Indexes
            $table->index('student_id', 'IDX_StudSpecialNeeds_student');
            $table->index('need_type', 'IDX_StudSpecialNeeds_type');
            $table->index(['start_date', 'end_date'], 'IDX_StudSpecialNeeds_dates');

            // Note: Application logic should validate date sequence (end_date >= start_date) if necessary.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_special_needs');
    }
};
