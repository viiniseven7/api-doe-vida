<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->index('hemocentro_id', 'idx_agend_hemocentro');
            $table->index('user_id', 'idx_agend_user');
            $table->index('status_agendamento', 'idx_agend_status');
            $table->index('data_hora_doacao', 'idx_agend_data');
        });

        Schema::table('doacao', function (Blueprint $table) {
            $table->index('hemocentro_id', 'idx_doacao_hemocentro');
            $table->index('user_id', 'idx_doacao_user');
            $table->index('tipo_sangue', 'idx_doacao_tipo');
            $table->index('data_hora_doacao', 'idx_doacao_data');
        });

        Schema::table('triagens', function (Blueprint $table) {
            $table->index('hemocentro_id', 'idx_triagens_hemocentro');
            $table->index('user_id', 'idx_triagens_user');
            $table->index('apto', 'idx_triagens_apto');
        });

        Schema::table('estoques', function (Blueprint $table) {
            $table->index('hemocentro_id', 'idx_estoques_hemocentro');
            $table->index('tipo_sangue', 'idx_estoques_tipo');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role_id', 'idx_users_role');
            $table->index('hemocentro_id', 'idx_users_hemocentro');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropIndex('idx_agend_hemocentro');
            $table->dropIndex('idx_agend_user');
            $table->dropIndex('idx_agend_status');
            $table->dropIndex('idx_agend_data');
        });
        Schema::table('doacao', function (Blueprint $table) {
            $table->dropIndex('idx_doacao_hemocentro');
            $table->dropIndex('idx_doacao_user');
            $table->dropIndex('idx_doacao_tipo');
            $table->dropIndex('idx_doacao_data');
        });
        Schema::table('triagens', function (Blueprint $table) {
            $table->dropIndex('idx_triagens_hemocentro');
            $table->dropIndex('idx_triagens_user');
            $table->dropIndex('idx_triagens_apto');
        });
        Schema::table('estoques', function (Blueprint $table) {
            $table->dropIndex('idx_estoques_hemocentro');
            $table->dropIndex('idx_estoques_tipo');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_hemocentro');
        });
    }
};
