<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criação da tabela
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BigInt
            $table->string('name'); 
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();

            // Dados do Doador
            $table->enum('tipo_sang', ['A+','A-','B+','B-','AB+','AB-','O+','O-'])->nullable();
            $table->enum('sexo', ['M','F','Outro'])->nullable();
            $table->date('data_nasc')->nullable();
            $table->string('cpf', 14)->unique()->nullable();
            $table->string('telefone', 20)->nullable();
            
            // Endereço
            $table->string('cep', 9)->nullable();
            $table->string('rua')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('complemento')->nullable();
            $table->integer('numero')->nullable();
            $table->char('uf', 2)->nullable();

            $table->boolean('status')->default(true);

            // COLUNAS DAS CHAVES (Usando unsignedInteger para bater com sua tabela Roles)
            $table->integer('role_id')->nullable();
            $table->integer('hemocentro_id')->nullable(); // Mudei aqui também por precaução!
            $table->unsignedBigInteger('criado_por')->nullable();

            // DATAS
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        // 2. CRIAÇÃO DAS REGRAS (Chaves e Nomes)
        Schema::table('users', function (Blueprint $table) {
            // Renomeia a coluna de delete para o padrão que você quer
            $table->renameColumn('deleted_at', 'deletado_em');

            // Chaves Estrangeiras
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('hemocentro_id')->references('id')->on('hemocentro')->onDelete('set null');
            $table->foreign('criado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};