<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
       Schema::create('triagem', function (Blueprint $table) {
    $table->increments('id');

    $table->unsignedInteger('id_user')->nullable();
    $table->unsignedInteger('id_user_funcionario')->nullable();
    $table->unsignedInteger('hemocentro_id')->nullable(); 

    $table->enum('tipo', ['doacao', 'avaliacao'])->nullable();
    $table->boolean('doenca')->nullable();
    $table->enum('lista_doencas', ['nenhuma', 'hepatite', 'hiv', 'outra'])->nullable();
    $table->enum('gravidez', ['sim', 'nao'])->nullable();
    $table->boolean('tatuagens')->nullable();
    $table->enum('tatuagem_recente', ['sim', 'nao'])->nullable();
    $table->boolean('medicamento')->nullable();
    $table->enum('lista_medicamentos', ['nenhum', 'antibiotico', 'outro'])->nullable();
    $table->enum('tipo_sangue', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
    $table->dateTime('data_hora_doacao')->nullable();
    $table->enum('status', ['aprovado', 'reprovado'])->nullable();

    $table->foreign('id_user', 'triagem_ibfk_1')
          ->references('id')->on('users')
          ->nullOnDelete();

    $table->foreign('id_user_funcionario', 'triagem_ibfk_2')
          ->references('id')->on('users')
          ->nullOnDelete();

    $table->foreign('hemocentro_id', 'triagem_ibfk_3')
          ->references('id')->on('hemocentro')
          ->nullOnDelete();
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
