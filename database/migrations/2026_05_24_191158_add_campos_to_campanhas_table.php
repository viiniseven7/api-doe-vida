<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            if (!Schema::hasColumn('campanhas', 'total_disparado')) {
                $table->integer('total_disparado')->default(0)->after('status');
            }
            if (!Schema::hasColumn('campanhas', 'total_aberto')) {
                $table->integer('total_aberto')->default(0)->after('total_disparado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campanhas', function (Blueprint $table) {
            $table->dropColumn(['total_disparado', 'total_aberto']);
        });
    }
};
