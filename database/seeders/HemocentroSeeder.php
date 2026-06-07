<?php

namespace Database\Seeders;

use App\Models\Hemocentro;
use Illuminate\Database\Seeder;

class HemocentroSeeder extends Seeder
{
    public function run(): void
    {
        Hemocentro::updateOrCreate(
            ['cnpj' => '56904583000130'],
            [
                'nome' => 'Hemepar Curitiba',
                'telefone' => '(41) 3281-4000',
                'email' => 'hemeparcuritiba@gmail.com',
                'bairro' => 'Alto da XV',
                'uf' => 'PR',
                'endereco' => 'Rua XV de Novembro',
                'cidade' => 'Curitiba',
                'numero' => '200',
                'razao_social' => 'Centro de Hematologia e Hemoterapia do Parana',
                'status_agendamento' => 'ativo',
                'status' => true,
            ]
        );

        Hemocentro::updateOrCreate(
            ['cnpj' => '569045830010395'],
            [
                'nome' => 'Hemocentro hospital de clinicas',
                'telefone' => '(41) 3281-1020',
                'email' => 'hemocentrogc@gmail.com',
                'bairro' => 'Centro',
                'uf' => 'PR',
                'endereco' => 'Joao Prodoscimo',
                'cidade' => 'Curitiba',
                'numero' => '18',
                'razao_social' => 'Hemocentro dos Hospitais de Clinicas',
                'status_agendamento' => 'ativo',
                'status' => true,
            ]
        );

        Hemocentro::updateOrCreate(
            ['cnpj' => '12345678000190'],
            [
                'nome' => 'Hemocentro Sao Jose dos Pinhais',
                'telefone' => '(41) 3381-2400',
                'email' => 'hemosjp@doevida.test',
                'bairro' => 'Centro',
                'uf' => 'PR',
                'endereco' => 'Rua Isabel A Redentora',
                'cidade' => 'Sao Jose dos Pinhais',
                'numero' => '1434',
                'razao_social' => 'Centro de Hemoterapia Sao Jose dos Pinhais',
                'status_agendamento' => 'ativo',
                'status' => true,
                'criado_por' => 'seeder',
            ]
        );

        Hemocentro::updateOrCreate(
            ['cnpj' => '98765432000110'],
            [
                'nome' => 'Hemocentro Araucaria',
                'telefone' => '(41) 3614-1500',
                'email' => 'hemoaraucaria@doevida.test',
                'bairro' => 'Fazenda Velha',
                'uf' => 'PR',
                'endereco' => 'Rua Pedro Druszcz',
                'cidade' => 'Araucaria',
                'numero' => '111',
                'razao_social' => 'Centro de Hemoterapia Araucaria',
                'status_agendamento' => 'ativo',
                'status' => true,
                'criado_por' => 'seeder',
            ]
        );
    }
}
