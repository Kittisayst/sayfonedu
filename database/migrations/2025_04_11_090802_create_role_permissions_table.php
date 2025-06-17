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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id('role_permission_id');
            $table->unsignedBigInteger('role_id')->comment('ລະຫັດບົດບາດ (FK ຈາກ Roles)');
            $table->unsignedBigInteger('permission_id')->comment('ລະຫັດສິດທິ (FK ຈາກ Permissions)');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id'], 'UQ_RolePermissions_role_perm');
            
            $table->foreign('role_id')->references('role_id')->on('roles');
            $table->foreign('permission_id')->references('permission_id')->on('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
