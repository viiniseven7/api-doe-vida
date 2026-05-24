<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doacao', function (Blueprint $table) {
            if (!Schema::hasColumn('doacao', 'estoque_lancado_em')) {
                $table->dateTime('estoque_lancado_em')->nullable()->after('atualizado_em');
            }

            if (!Schema::hasColumn('doacao', 'estoque_lancado_por')) {
                $table->foreignId('estoque_lancado_por')
                    ->nullable()
                    ->after('estoque_lancado_em')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('doacao', function (Blueprint $table) {
            if (Schema::hasColumn('doacao', 'estoque_lancado_por')) {
                $table->dropConstrainedForeignId('estoque_lancado_por');
            }

            if (Schema::hasColumn('doacao', 'estoque_lancado_em')) {
                $table->dropColumn('estoque_lancado_em');
            }
        });
    }
};
