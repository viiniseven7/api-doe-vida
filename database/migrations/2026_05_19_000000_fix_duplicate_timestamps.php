<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hemocentros: remover created_at e updated_at redundantes
        Schema::table('hemocentros', function (Blueprint $table) {
            if (Schema::hasColumn('hemocentros', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('hemocentros', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        // Doacao: remover created_at e updated_at redundantes
        Schema::table('doacao', function (Blueprint $table) {
            if (Schema::hasColumn('doacao', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('doacao', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hemocentros', function (Blueprint $table) {
            $table->timestamps();
        });
        Schema::table('doacao', function (Blueprint $table) {
            $table->timestamps();
        });
    }
};
