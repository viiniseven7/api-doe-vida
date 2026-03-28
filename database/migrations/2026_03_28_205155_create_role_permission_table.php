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
        Schema::create('role_permission', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_permission')->nullable();
            $table->integer('id_role')->nullable();

            $table->foreign('id_permission', 'role_permission_ibfk_1')->references('id')->on('permissions');
            $table->foreign('id_role', 'role_permission_ibfk_2')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission');
    }
};
