<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleDoador = $this->role('doador');
        $roleFuncionario = $this->role('funcionario');
        $roleDiretor = $this->role('diretor');
        $roleAdmin = $this->role('admin');
        
        $tipos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $bairros = ['Centro', 'Jardins', 'Vila Maria', 'Itaquera', 'Pinheiros', 'Lapa', 'Santo Amaro', 'Moema', 'Morumbi', 'Santana'];
        $cidades = ['São Paulo', 'Santo André', 'São Bernardo', 'São Caetano'];

        // Criar 30 Doadores diversos
        for ($i = 1; $i <= 30; $i++) {
            $dataNasc = Carbon::now()->subYears(rand(16, 65))->subDays(rand(1, 365));
            
            $user = User::updateOrCreate(
                ['email' => "doador$i@email.com"],
                [
                    'name' => "Doador " . ($i % 2 == 0 ? "Masculino " : "Feminino ") . $i,
                    'password' => Hash::make('password'),
                    'cpf' => str_pad($i, 11, '0', STR_PAD_LEFT),
                    'tipo_sang' => $tipos[array_rand($tipos)],
                    'sexo' => ($i % 2 == 0) ? 'M' : 'F',
                    'telefone' => "(11) 9" . rand(7000, 9999) . "-" . rand(1000, 9999),
                    'data_nasc' => $dataNasc->toDateString(),
                    'role_id' => $roleDoador->id,
                    'bairro' => $bairros[array_rand($bairros)],
                    'cidade' => $cidades[array_rand($cidades)],
                    'uf' => 'SP',
                    'status' => true,
                ]
            );

            $user->syncRoles([$roleDoador]);
        }

        // Funcionários e Diretores
        $this->createStaff('funcionario1@email.com', 'Funcionario Centro', $roleFuncionario, 1);
        $this->createStaff('funcionario2@email.com', 'Funcionario Norte', $roleFuncionario, 2);
        $this->createStaff('diretor@email.com', 'Diretor Geral Unidade 1', $roleDiretor, 1);
        
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@email.com'],
            [
                'name' => 'Admin Sistema',
                'password' => Hash::make('password'),
                'cpf' => '44444444444',
                'role_id' => $roleAdmin->id,
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        $this->command->info('30 Doadores e equipe técnica criados com sucesso!');
    }

    private function createStaff($email, $name, $role, $hemocentroId)
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'cpf' => rand(100000000, 999999999) . '00',
                'role_id' => $role->id,
                'hemocentro_id' => $hemocentroId,
            ]
        );
        $user->syncRoles([$role]);
    }

    private function role(string $name): Role
    {
        return Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'api',
        ]);
    }
}
