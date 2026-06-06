<?php

namespace Database\Seeders;

use App\Models\Doacao;
use App\Models\Hemocentro;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DoacaoSeeder extends Seeder
{
    public function run(): void
    {
        $doadores = User::where('role_id', 1)->get();
        $funcionarios = User::where('role_id', 2)->get();
        $hemocentros = Hemocentro::where('status', 1)->get();
        $tiposSangue = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $minutos = [0, 15, 30, 45];

        if ($doadores->isEmpty() || $funcionarios->isEmpty() || $hemocentros->isEmpty()) {
            $this->command->warn('Doacoes nao criadas: faltam doadores, funcionarios ou hemocentros.');
            return;
        }

        for ($i = 0; $i < 20; $i++) {
            $doador = $doadores->random();
            $dataDoacao = Carbon::now()
                ->subDays(rand(1, 180))
                ->setTime(rand(8, 17), $minutos[array_rand($minutos)]);

            Doacao::create([
                'user_id' => $doador->id,
                'hemocentro_id' => $hemocentros->random()->id,
                'funcionario_id' => $funcionarios->random()->id,
                'data_hora_doacao' => $dataDoacao,
                'tipo_sangue' => $doador->tipo_sang ?? $tiposSangue[array_rand($tiposSangue)],
                'quantidade' => 450,
                'data_validade_sangue' => $dataDoacao->copy()->addDays(35),
                'atualizado_em' => now(),
            ]);
        }

        $this->command->info('20 doacoes de exemplo criadas!');
    }
}
