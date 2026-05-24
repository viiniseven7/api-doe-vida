<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Doacao;
use App\Models\Hemocentro;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class DevSeedController extends Controller
{
    public function seedDoadores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hemocentro_id' => 'nullable|exists:hemocentros,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados invalidos para o seed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $hemocentro = $request->filled('hemocentro_id')
            ? Hemocentro::find($request->integer('hemocentro_id'))
            : Hemocentro::query()->where('status', '1')->orderBy('id')->first();

        if (!$hemocentro) {
            return response()->json([
                'message' => 'Nenhum hemocentro encontrado para vincular as doacoes.',
            ], 404);
        }

        $funcionario = DB::transaction(function () use ($hemocentro) {
            $funcionarioExistente = $this->ensureFuncionarioSeed($hemocentro);
            $created = $this->ensureSeedDoadores($hemocentro, $funcionarioExistente);

            return [
                'funcionario' => [
                    'id' => $funcionarioExistente->id,
                    'nome' => $funcionarioExistente->name,
                    'email' => $funcionarioExistente->email,
                    'hemocentro_id' => $funcionarioExistente->hemocentro_id,
                ],
                'doadores' => $created,
            ];
        });

        return response()->json([
            'message' => 'Seed de doadores concluido com sucesso.',
            'hemocentro' => [
                'id' => $hemocentro->id,
                'nome' => $hemocentro->nome,
                'cidade' => $hemocentro->cidade,
            ],
            'funcionario' => $funcionario['funcionario'],
            'doadores' => $funcionario['doadores'],
            'total_doadores' => count($funcionario['doadores']),
        ]);
    }

    public function seedAgendamentos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hemocentro_id' => 'nullable|exists:hemocentros,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados invalidos para o seed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $hemocentro = $request->filled('hemocentro_id')
            ? Hemocentro::find($request->integer('hemocentro_id'))
            : Hemocentro::query()->where('status', '1')->orderBy('id')->first();

        if (!$hemocentro) {
            return response()->json([
                'message' => 'Nenhum hemocentro encontrado para vincular os agendamentos.',
            ], 404);
        }

        $resultado = DB::transaction(function () use ($hemocentro) {
            $funcionario = $this->ensureFuncionarioSeed($hemocentro);
            $doadores = collect($this->ensureSeedDoadores($hemocentro, $funcionario))
                ->pluck('user_id')
                ->all();

            $users = User::query()
                ->whereIn('id', $doadores)
                ->orderBy('id')
                ->get()
                ->values();

            if ($users->count() < 10) {
                return [
                    'created' => [],
                    'funcionario' => $funcionario,
                ];
            }

            $baseDate = Carbon::today();
            $appointments = [
                ['offset_days' => 0,  'time' => '08:30:00', 'status' => 'AGE'],
                ['offset_days' => 0,  'time' => '10:00:00', 'status' => 'CON'],
                ['offset_days' => 0,  'time' => '11:30:00', 'status' => 'CAN'],
                ['offset_days' => 0,  'time' => '14:00:00', 'status' => 'FIN'],
                ['offset_days' => 1,  'time' => '09:15:00', 'status' => 'AGE'],
                ['offset_days' => 1,  'time' => '13:45:00', 'status' => 'CON'],
                ['offset_days' => 2,  'time' => '15:00:00', 'status' => 'EXC'],
                ['offset_days' => -1, 'time' => '09:00:00', 'status' => 'FIN'],
                ['offset_days' => -2, 'time' => '10:30:00', 'status' => 'CAN'],
                ['offset_days' => 3,  'time' => '16:00:00', 'status' => 'AGE'],
            ];

            $created = [];

            foreach ($appointments as $index => $config) {
                $donor = $users[$index];
                $appointmentDate = $baseDate->copy()
                    ->addDays($config['offset_days'])
                    ->setTimeFromTimeString($config['time']);

                $agendamento = Agendamento::updateOrCreate(
                    [
                        'user_id' => $donor->id,
                        'hemocentro_id' => $hemocentro->id,
                        'data_hora_doacao' => $appointmentDate->format('Y-m-d H:i:s'),
                    ],
                    [
                        'status_agendamento' => $config['status'],
                        'coletador_id' => in_array($config['status'], ['CON', 'FIN'], true) ? $funcionario->id : null,
                    ]
                );

                $created[] = [
                    'agendamento_id' => $agendamento->id,
                    'doador' => $donor->name,
                    'status' => $agendamento->status_agendamento,
                    'data_hora_doacao' => $agendamento->data_hora_doacao,
                    'hemocentro_id' => $hemocentro->id,
                ];
            }

            return [
                'created' => $created,
                'funcionario' => $funcionario,
            ];
        });

        if (empty($resultado['created'])) {
            return response()->json([
                'message' => 'Nao foi possivel montar os agendamentos de seed.',
            ], 409);
        }

        return response()->json([
            'message' => 'Seed de agendamentos concluido com sucesso.',
            'hemocentro' => [
                'id' => $hemocentro->id,
                'nome' => $hemocentro->nome,
            ],
            'funcionario' => [
                'id' => $resultado['funcionario']->id,
                'nome' => $resultado['funcionario']->name,
                'email' => $resultado['funcionario']->email,
            ],
            'agendamentos' => $resultado['created'],
            'status_utilizados' => ['AGE', 'CON', 'CAN', 'EXC', 'FIN'],
            'observacao' => 'O sistema nao possui status "ERRO" em agendamento; os status validos atuais sao AGE, CON, CAN, EXC e FIN.',
        ]);
    }

    private function ensureFuncionarioSeed(Hemocentro $hemocentro): User
    {
        $roleFuncionario = Role::firstOrCreate([
            'name' => 'funcionario',
            'guard_name' => 'api',
        ]);

        $funcionario = User::query()
            ->where('hemocentro_id', $hemocentro->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'funcionario');
            })
            ->first();

        if (!$funcionario) {
            $funcionario = User::updateOrCreate(
                ['email' => sprintf('seed.funcionario.hemo%d@doevida.local', $hemocentro->id)],
                [
                    'name' => 'Funcionario Seed Hemocentro ' . $hemocentro->id,
                    'password' => Hash::make('password'),
                    'cpf' => $this->formatCpfForSeed(90000000000 + $hemocentro->id),
                    'telefone' => '(41) 98888-000' . $hemocentro->id,
                    'sexo' => 'Outro',
                    'status' => true,
                    'role_id' => $roleFuncionario->id,
                    'hemocentro_id' => $hemocentro->id,
                ]
            );
        }

        $funcionario->syncRoles([$roleFuncionario]);

        return $funcionario;
    }

    private function ensureSeedDoadores(Hemocentro $hemocentro, User $funcionario): array
    {
        $roleDoador = Role::firstOrCreate([
            'name' => 'doador',
            'guard_name' => 'api',
        ]);

        $donors = [
            ['name' => 'Ana Clara Souza', 'email' => 'seed.ana.clara@doevida.local', 'cpf' => '90100000001', 'sexo' => 'F', 'tipo_sang' => 'A+', 'data_nasc' => '1998-02-14', 'cidade' => 'Curitiba', 'uf' => 'PR', 'telefone' => '(41) 99111-0001', 'data_doacao' => '2026-02-10 09:00:00'],
            ['name' => 'Bruno Henrique Lima', 'email' => 'seed.bruno.lima@doevida.local', 'cpf' => '90100000002', 'sexo' => 'M', 'tipo_sang' => 'O+', 'data_nasc' => '1991-07-21', 'cidade' => 'Colombo', 'uf' => 'PR', 'telefone' => '(41) 99111-0002', 'data_doacao' => '2026-02-13 10:30:00'],
            ['name' => 'Carla Mendes Rocha', 'email' => 'seed.carla.rocha@doevida.local', 'cpf' => '90100000003', 'sexo' => 'F', 'tipo_sang' => 'AB+', 'data_nasc' => '1987-11-03', 'cidade' => 'Pinhais', 'uf' => 'PR', 'telefone' => '(41) 99111-0003', 'data_doacao' => '2026-02-18 14:00:00'],
            ['name' => 'Diego Martins', 'email' => 'seed.diego.martins@doevida.local', 'cpf' => '90100000004', 'sexo' => 'M', 'tipo_sang' => 'B-', 'data_nasc' => '1995-01-29', 'cidade' => 'Araucaria', 'uf' => 'PR', 'telefone' => '(41) 99111-0004', 'data_doacao' => '2026-02-21 08:45:00'],
            ['name' => 'Eduarda Nascimento', 'email' => 'seed.eduarda.nascimento@doevida.local', 'cpf' => '90100000005', 'sexo' => 'F', 'tipo_sang' => 'O-', 'data_nasc' => '2000-05-11', 'cidade' => 'Sao Jose dos Pinhais', 'uf' => 'PR', 'telefone' => '(41) 99111-0005', 'data_doacao' => '2026-03-01 11:20:00'],
            ['name' => 'Felipe Augusto Pereira', 'email' => 'seed.felipe.pereira@doevida.local', 'cpf' => '90100000006', 'sexo' => 'M', 'tipo_sang' => 'A-', 'data_nasc' => '1989-09-17', 'cidade' => 'Curitiba', 'uf' => 'PR', 'telefone' => '(41) 99111-0006', 'data_doacao' => '2026-03-05 15:10:00'],
            ['name' => 'Gabriela Torres', 'email' => 'seed.gabriela.torres@doevida.local', 'cpf' => '90100000007', 'sexo' => 'F', 'tipo_sang' => 'B+', 'data_nasc' => '1993-03-24', 'cidade' => 'Campo Largo', 'uf' => 'PR', 'telefone' => '(41) 99111-0007', 'data_doacao' => '2026-03-09 09:40:00'],
            ['name' => 'Henrique Oliveira Santos', 'email' => 'seed.henrique.santos@doevida.local', 'cpf' => '90100000008', 'sexo' => 'M', 'tipo_sang' => 'AB-', 'data_nasc' => '1985-12-08', 'cidade' => 'Almirante Tamandare', 'uf' => 'PR', 'telefone' => '(41) 99111-0008', 'data_doacao' => '2026-03-14 13:15:00'],
            ['name' => 'Isabela Costa', 'email' => 'seed.isabela.costa@doevida.local', 'cpf' => '90100000009', 'sexo' => 'F', 'tipo_sang' => 'A+', 'data_nasc' => '1999-06-30', 'cidade' => 'Curitiba', 'uf' => 'PR', 'telefone' => '(41) 99111-0009', 'data_doacao' => '2026-03-20 10:05:00'],
            ['name' => 'Joao Pedro Ribeiro', 'email' => 'seed.joao.ribeiro@doevida.local', 'cpf' => '90100000010', 'sexo' => 'M', 'tipo_sang' => 'O+', 'data_nasc' => '1996-08-19', 'cidade' => 'Fazenda Rio Grande', 'uf' => 'PR', 'telefone' => '(41) 99111-0010', 'data_doacao' => '2026-03-26 16:30:00'],
        ];

        $created = [];

        foreach ($donors as $index => $data) {
            $doador = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'cpf' => $this->formatCpfForSeed($data['cpf']),
                    'telefone' => $data['telefone'],
                    'tipo_sang' => $data['tipo_sang'],
                    'sexo' => $data['sexo'],
                    'data_nasc' => $data['data_nasc'],
                    'cidade' => $data['cidade'],
                    'uf' => $data['uf'],
                    'status' => true,
                    'role_id' => $roleDoador->id,
                    'tempo_restricao' => Carbon::parse($data['data_doacao'])->addDays($data['sexo'] === 'M' ? 90 : 120)->toDateString(),
                ]
            );

            $doador->syncRoles([$roleDoador]);

            $doacao = Doacao::updateOrCreate(
                [
                    'user_id' => $doador->id,
                    'hemocentro_id' => $hemocentro->id,
                ],
                [
                    'funcionario_id' => $funcionario->id,
                    'data_hora_doacao' => $data['data_doacao'],
                    'tipo_sangue' => $data['tipo_sang'],
                    'quantidade' => 450,
                    'data_validade_sangue' => Carbon::parse($data['data_doacao'])->addDays(35)->format('Y-m-d H:i:s'),
                    'atualizado_em' => now(),
                ]
            );

            $created[] = [
                'user_id' => $doador->id,
                'nome' => $doador->name,
                'email' => $doador->email,
                'tipo_sanguineo' => $doador->tipo_sang,
                'sexo' => $doador->sexo,
                'idade' => Carbon::parse($doador->data_nasc)->age,
                'doacao_id' => $doacao->id,
                'hemocentro_id' => $hemocentro->id,
                'ordem_seed' => $index + 1,
            ];
        }

        return $created;
    }

    private function formatCpfForSeed(int|string $cpf): string
    {
        return str_pad((string) preg_replace('/\D+/', '', (string) $cpf), 11, '0', STR_PAD_LEFT);
    }
}
