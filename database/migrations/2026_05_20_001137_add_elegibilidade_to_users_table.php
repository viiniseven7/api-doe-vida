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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('apto_pelo_autoexame')->default(false)->after('status');
            $table->timestamp('autoexame_validade')->nullable()->after('apto_pelo_autoexame');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apto_pelo_autoexame', 'autoexame_validade']);
        });
    }
};
