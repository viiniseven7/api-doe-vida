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
        Schema::create('doacao', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_user')->nullable();
            $table->integer('hemocentro_id')->nullable();
            $table->integer('id_user_func')->nullable();
            $table->dateTime('data_hora_doacao')->nullable();
            $table->dateTime('data_validade_sangue')->nullable();
            $table->dateTime('retirada_em')->nullable();
            $table->integer('retirado_por')->nullable();
            $table->enum('tipo_sangue', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->float('quantidade')->nullable();
            $table->float('quantidade_retirada')->nullable();
            $table->dateTime('atualizado_em')->nullable();
            $table->timestamps();

            $table->foreign('id_user', 'doacao_ibfk_1')->references('id')->on('usuario');
            $table->foreign('hemocentro_id', 'doacao_ibfk_2')->references('id')->on('hemocentro');
            $table->foreign('id_user_func', 'doacao_ibfk_3')->references('id')->on('usuario');
            $table->foreign('retirado_por', 'doacao_ibfk_4')->references('id')->on('usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doacao');
    }
};
