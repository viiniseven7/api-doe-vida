<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triagem_sinais_vitais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('triagem_id')
                ->constrained('triagens')
                ->onDelete('cascade');
            $table->float('peso')->nullable()
                ->comment('Peso em kg');
            $table->smallInteger('pressao_sistolica')->unsigned()->nullable()
                ->comment('Pressao sistolica em mmHg');
            $table->smallInteger('pressao_diastolica')->unsigned()->nullable()
                ->comment('Pressao diastolica em mmHg');
            $table->float('temperatura')->nullable()
                ->comment('Temperatura axilar em graus Celsius');
            $table->smallInteger('frequencia_cardiaca')->unsigned()->nullable()
                ->comment('Frequencia cardiaca em bpm');
            $table->float('hemoglobina')->nullable()
                ->comment('Hemoglobina em g/dL - minimo 12.5 mulheres, 13.0 homens');
            $table->float('hematocrito')->nullable()
                ->comment('Hematocrito em %');
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triagem_sinais_vitais');
    }
};
