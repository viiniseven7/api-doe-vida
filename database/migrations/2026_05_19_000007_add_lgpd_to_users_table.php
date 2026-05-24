<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('lgpd_aceite')->default(false)->after('status')
                ->comment('Se o doador aceitou os termos de privacidade');
            $table->timestamp('lgpd_aceite_em')->nullable()->after('lgpd_aceite')
                ->comment('Quando o aceite foi registrado');
            $table->string('lgpd_ip', 45)->nullable()->after('lgpd_aceite_em')
                ->comment('IP do dispositivo no momento do aceite - IPv4 ou IPv6');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lgpd_aceite', 'lgpd_aceite_em', 'lgpd_ip']);
        });
    }
};
