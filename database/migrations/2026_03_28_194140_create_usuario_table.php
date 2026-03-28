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
        Schema::create('usuario', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nome', 255)->nullable();
            $table->enum('tipo_sang', ['A+','A-','B+','B-','AB+','AB-','O+','O-'])->nullable();
            $table->enum('sexo', ['M','F','Outro'])->nullable();
            $table->date('data_nasc')->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('senha', 255)->nullable();
            $table->integer('hemocentro_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->string('name_role', 255)->nullable();
            $table->integer('telefone')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('rua', 255)->nullable();
            $table->string('bairro', 255)->nullable();
            $table->integer('cep')->nullable();
            $table->string('cidade', 255)->nullable();
            $table->string('complemento', 255)->nullable();
            $table->integer('numero')->nullable();
            $table->boolean('status')->nullable();
            $table->char('uf', 2)->nullable();
            $table->date('criado_em')->nullable();
            $table->date('atualizado_em')->nullable();
            $table->date('deletado_em')->nullable();
            $table->timestamps();

            // Chaves Estrangeiras
            $table->foreign('hemocentro_id', 'usuario_ibfk_1')->references('id')->on('hemocentro');
            $table->foreign('role_id', 'usuario_ibfk_2')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
