<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teacher_tasks', function (Blueprint $table) {
            // ລະຫັດວຽກທີ່ເພີ່ມຂຶ້ນແບບອັດຕະໂນມັດ
            $table->id('task_id')->comment('ລະຫັດເອກະລັກຂອງວຽກທີ່ເພີ່ມອັດຕະໂນມັດ');

            // ຂໍ້ມູນພື້ນຖານ
            $table->string('title')->comment('ຫົວຂໍ້ຫຼືຊື່ຂອງວຽກທີ່ມອບໝາຍ');
            $table->text('description')->nullable()->comment('ຄຳອະທິບາຍລາຍລະອຽດຂອງວຽກ');
            $table->unsignedBigInteger('assigned_by')->comment('ລະຫັດຜູ້ບໍລິຫານທີ່ເປັນຜູ້ມອບໝາຍວຽກ');
            $table->unsignedBigInteger('assigned_to')->comment('ລະຫັດຄູທີ່ໄດ້ຮັບມອບໝາຍວຽກ');

            // ຂໍ້ມູນການຈັດການ
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->comment('ລະດັບຄວາມສຳຄັນຂອງວຽກ: ຕໍ່າ, ປານກາງ, ສູງ');
            $table->date('start_date')->comment('ວັນທີເລີ່ມຕົ້ນຂອງວຽກ');
            $table->date('due_date')->comment('ວັນທີກຳນົດສົ່ງວຽກ');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'overdue'])->default('pending')->comment('ສະຖານະປະຈຸບັນຂອງວຽກ: ລໍຖ້າ, ກຳລັງດຳເນີນການ, ສຳເລັດແລ້ວ, ເກີນກຳນົດ');
            $table->integer('progress')->default(0)->comment('ເປີເຊັນຄວາມຄືບໜ້າຂອງວຽກ (0-100)');

            // ຂໍ້ມູນການອັບເດດຄວາມຄືບໜ້າ
            $table->text('latest_update')->nullable()->comment('ບັນທຶກການອັບເດດຄວາມຄືບໜ້າຫຼ້າສຸດ');
            $table->longText('update_history')->nullable()->comment('ປະຫວັດການອັບເດດທັງໝົດໃນຮູບແບບ JSON');

            // ຂໍ້ມູນຄຳເຫັນ
            $table->longText('comments')->nullable()->comment('ຄຳເຫັນແລະການສົນທະນາກ່ຽວກັບວຽກໃນຮູບແບບ JSON');

            // ຂໍ້ມູນຜົນສຳເລັດ
            $table->text('completion_note')->nullable()->comment('ບັນທຶກຫຼືໝາຍເຫດເມື່ອວຽກສຳເລັດແລ້ວ');
            $table->dateTime('completion_date')->nullable()->comment('ວັນທີແລະເວລາທີ່ສຳເລັດວຽກ');
            $table->tinyInteger('rating')->nullable()->comment('ຄະແນນປະເມີນຄຸນນະພາບຂອງວຽກທີ່ສຳເລັດ (1-5)');

            // ຂໍ້ມູນການເຮັດວຽກລະບົບ
            $table->timestamps();

            // ຂໍ້ຈຳກັດ Foreign Key
            $table->foreign('assigned_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('teachers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_tasks');
    }
};
