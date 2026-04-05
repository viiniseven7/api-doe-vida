<?php   

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('responsavel_nome')->nullable();
            $table->string('responsavel_cpf', 14)->nullable();
            $table->date('responsavel_data_nasc')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['responsavel_nome', 'responsavel_cpf']);
        });
    }
};