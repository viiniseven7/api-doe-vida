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
        Schema::create('agendamento', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_doador')->nullable();
            $table->integer('hemocentro_id')->nullable();
            $table->dateTime('data_hora_doacao')->nullable();
            $table->enum('status_agendamento', ['agendado', 'cancelado', 'concluido'])->nullable();
            $table->dateTime('criado_em')->nullable();
            $table->dateTime('atualizado_em')->nullable();
            $table->dateTime('deletado_em')->nullable();
            $table->timestamps();

            $table->foreign('id_doador', 'agendamento_ibfk_1')->references('id')->on('usuario');
            $table->foreign('hemocentro_id', 'agendamento_ibfk_2')->references('id')->on('hemocentro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamento');
    }
};
