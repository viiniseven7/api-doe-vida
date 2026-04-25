<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tabela roles do Spatie usa 'name' e 'guard_name'
        \DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Doador', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Funcionario', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Diretor', 'guard_name' => 'web'],
        ]);
    }
        
    
}
