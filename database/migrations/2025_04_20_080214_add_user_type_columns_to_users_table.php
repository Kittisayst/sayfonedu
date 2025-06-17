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
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type')->nullable()->after('status');
            $table->unsignedBigInteger('related_id')->nullable()->after('user_type');

            // ສ້າງ composite foreign key ທີ່ຂຶ້ນກັບ user_type
            $table->foreign(['user_type', 'related_id'])
                ->references(['user_type', 'id'])
                ->on('teachers')
                ->onDelete('set null')
                ->where('user_type', '=', 'teacher');

            $table->foreign(['user_type', 'related_id'])
                ->references(['user_type', 'id'])
                ->on('parents')
                ->onDelete('set null')
                ->where('user_type', '=', 'parent');

            $table->foreign(['user_type', 'related_id'])
                ->references(['user_type', 'id'])
                ->on('students')
                ->onDelete('set null')
                ->where('user_type', '=', 'student');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_type', 'related_id']);
            $table->dropColumn(['user_type', 'related_id']);
        });
    }
};
