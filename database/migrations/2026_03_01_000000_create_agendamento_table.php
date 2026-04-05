<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamento', function (Blueprint $table) {
            $table->increments('id'); 
            
            
            $table->unsignedInteger('user_id')->nullable(); 
            $table->unsignedInteger('hemocentro_id')->nullable();
            
            $table->dateTime('data_hora_doacao')->nullable();
            $table->enum('status_agendamento', ['agendado', 'cancelado', 'concluido'])->default('agendado');

            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();

            $table->softDeletes()->renameColumn('deleted_at', 'deletado_em');
        });

        Schema::table('agendamento', function (Blueprint $table) {
            $table->renameColumn('deleted_at', 'deletado_em');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade'); 
                

            $table->foreign('hemocentro_id')
                  ->references('id')->on('hemocentro')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamento');
    }
};