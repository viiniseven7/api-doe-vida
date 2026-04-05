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
            $table->increments('id');            
            $table->unsignedInteger('id_permission')->nullable();
            $table->unsignedInteger('id_role')->nullable();

            $table->foreign('id_permission')->references('id')->on('permissions');
            $table->foreign('id_role')->references('id')->on('roles');
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
