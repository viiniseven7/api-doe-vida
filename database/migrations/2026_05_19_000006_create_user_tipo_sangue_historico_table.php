<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_tipo_sangue_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('tipo_sangue_anterior', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])
                ->nullable()
                ->comment('Null = primeiro registro de tipo sanguineo');
            $table->enum('tipo_sangue_novo', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->foreignId('alterado_por')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Funcionario que realizou a alteracao');
            $table->enum('categoria_motivo', [
                'erro_cadastro',
                'confirmacao_laboratorial',
                'retificacao_com_laudo',
                'retificacao_profissional',
            ]);
            $table->timestamp('alterado_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tipo_sangue_historico');
    }
};
