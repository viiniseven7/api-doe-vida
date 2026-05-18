<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\User;
use App\Models\Hemocentro;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AgendamentoSeeder extends Seeder
{
    public function run(): void
    {
        Agendamento::truncate();
        $doadores = User::where('role_id', 1)->get();
        $hemocentros = Hemocentro::all();
        $status = ['AGE', 'CAN', 'CON', 'EXC'];

        if ($doadores->isEmpty() || $hemocentros->isEmpty()) return;

        // 1. Criar 50 agendamentos PASSADOS (histórico)
        for ($i = 0; $i < 50; $i++) {
            $data = Carbon::now()->subDays(rand(1, 30))->addHours(rand(-12, 12));
            
            Agendamento::create([
                'user_id' => $doadores->random()->id,
                'hemocentro_id' => $hemocentros->random()->id,
                'data_hora_doacao' => $data,
                'status_agendamento' => $status[array_rand($status)],
                'criado_em' => $data->copy()->subDays(1),
            ]);
        }

        // 2. Criar 30 agendamentos FUTUROS (próximos 15 dias)
        // Isso é crucial para testar a agenda do funcionário e do doador
        for ($i = 0; $i < 30; $i++) {
            $data = Carbon::now()->addDays(rand(1, 15))->setHour(rand(8, 17))->setMinute(0);
            
            Agendamento::create([
                'user_id' => $doadores->random()->id,
                'hemocentro_id' => $hemocentros->random()->id,
                'data_hora_doacao' => $data,
                'status_agendamento' => 'AGE', // Futuros sempre começam como AGENDADO
                'criado_em' => Carbon::now(),
            ]);
        }

        $this->command->info('80 Agendamentos (passados e futuros) criados!');
    }
}
