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
        Schema::create('triagem', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_user')->nullable();
            $table->integer('id_user_funcionario')->nullable();
            $table->enum('tipo', ['doacao', 'avaliacao'])->nullable();
            $table->boolean('doenca')->nullable();
            $table->enum('lista_doencas', ['nenhuma', 'hepatite', 'hiv', 'outra'])->nullable();
            $table->integer('hemocentro_id')->nullable();
            $table->enum('gravidez', ['sim', 'nao'])->nullable();
            $table->boolean('tatuagens')->nullable();
            $table->enum('tatuagem_recente', ['sim', 'nao'])->nullable();
            $table->boolean('medicamento')->nullable();
            $table->enum('lista_medicamentos', ['nenhum', 'antibiotico', 'outro'])->nullable();
            $table->enum('tipo_sangue', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->dateTime('data_hora_doacao')->nullable();
            $table->enum('status', ['aprovado', 'reprovado'])->nullable();

            $table->foreign('id_user', 'triagem_ibfk_1')->references('id')->on('usuario');
            $table->foreign('id_user_funcionario', 'triagem_ibfk_2')->references('id')->on('usuario');
            $table->foreign('hemocentro_id', 'triagem_ibfk_3')->references('id')->on('hemocentro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triagem');
    }
};
