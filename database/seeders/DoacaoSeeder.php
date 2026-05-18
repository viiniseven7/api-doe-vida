<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doacao;
use App\Models\User;
use App\Models\Hemocentro;
use Carbon\Carbon;

class DoacaoSeeder extends Seeder
{
    public function run(): void
    {
        Doacao::truncate();
        $doadores = User::where('role_id', 1)->get();
        $funcionarios = User::where('role_id', 2)->get();
        $hemocentros = Hemocentro::all();

        if ($doadores->isEmpty() || $hemocentros->isEmpty()) return;

        // Criar 100 Doações para ter volume estatístico
        for ($i = 0; $i < 100; $i++) {
            $doador = $doadores->random();
            // Data aleatória nos últimos 12 meses para testar gráfico mensal
            $dataDoacao = Carbon::now()->subDays(rand(0, 365))->subHours(rand(0, 23));
            
            Doacao::create([
                'user_id'              => $doador->id,
                'hemocentro_id'        => $hemocentros->random()->id,
                'funcionario_id'       => $funcionarios->random()->id,
                'data_hora_doacao'     => $dataDoacao,
                'tipo_sangue'          => $doador->tipo_sang ?? 'O+',
                'quantidade'           => rand(400, 500),
                'data_validade_sangue' => $dataDoacao->copy()->addDays(35),
            ]);
        }

        $this->command->info('100 Doações históricas criadas para testes de relatórios!');
    }
}
