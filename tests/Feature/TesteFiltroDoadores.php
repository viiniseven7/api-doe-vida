<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Doacao;
use App\Models\Hemocentro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesteFiltroDoadores extends TestCase
{
    use RefreshDatabase;

    public function test_busca_de_doadores_com_filtro_de_hemocentro()
    {
        // 1. Setup
        $hemocentro1 = Hemocentro::factory()->create();
        $hemocentro2 = Hemocentro::factory()->create();

        $funcionario = User::factory()->create([
            'role_id' => 2, // Funcionario
            'hemocentro_id' => $hemocentro1->id,
        ]);

        $doadorNoHemo1 = User::factory()->create(['name' => 'Doador A']);
        Doacao::factory()->create(['user_id' => $doadorNoHemo1->id, 'hemocentro_id' => $hemocentro1->id]);

        $doadorNoHemo2 = User::factory()->create(['name' => 'Doador B']);
        Doacao::factory()->create(['user_id' => $doadorNoHemo2->id, 'hemocentro_id' => $hemocentro2->id]);

        // 2. Ação
        $this->actingAs($funcionario);
        $response = $this->getJson('/api/users?name=Doador');

        // 3. Asserção
        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Doador A']);
        $response->assertJsonMissing(['name' => 'Doador B']);
    }
}
