<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('triagem_perguntas', function (Blueprint $table) {
            $table->tinyInteger('bloco')->unsigned()->default(1)->after('pergunta')
                ->comment('0=pre_triagem, 1=estado_geral, 3=historico_recente, 4=historico_comportamental');
        });
    }

    public function down(): void
    {
        Schema::table('triagem_perguntas', function (Blueprint $table) {
            $table->dropColumn('bloco');
        });
    }
};
