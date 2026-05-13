<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_and_reset_password_with_valid_code(): void
    {
        $user = $this->createUserWithRole('doador', [
            'email' => 'reset@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $forgotResponse = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $forgotResponse->assertOk()
            ->assertJsonStructure(['message', 'codeForDev'])
            ->assertJsonMissingPath('emailSent');

        $token = $forgotResponse->json('codeForDev');

        $resetResponse = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NovaSenha123',
            'password_confirmation' => 'NovaSenha123',
        ]);

        $resetResponse->assertOk()
            ->assertJsonPath('message', 'Senha redefinida com sucesso.');

        $user->refresh();

        $this->assertTrue(Hash::check('NovaSenha123', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_reset_code_expires_after_fifteen_minutes(): void
    {
        $user = $this->createUserWithRole('doador', [
            'email' => 'expirado@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $forgotResponse = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $token = $forgotResponse->json('codeForDev');

        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update([
                'created_at' => Carbon::now()->subMinutes(16),
            ]);

        $resetResponse = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NovaSenha123',
            'password_confirmation' => 'NovaSenha123',
        ]);

        $resetResponse->assertStatus(422)
            ->assertJsonPath('error', 'Codigo expirado. Solicite uma nova recuperacao.');
    }

    public function test_user_can_reset_password_with_portuguese_field_names(): void
    {
        $user = $this->createUserWithRole('doador', [
            'email' => 'campos-portugues@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $forgotResponse = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $codigo = $forgotResponse->json('codeForDev');

        $resetResponse = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'codigo' => $codigo,
            'senha' => 'NovaSenha123',
            'confirmar_senha' => 'NovaSenha123',
        ]);

        $resetResponse->assertOk()
            ->assertJsonPath('message', 'Senha redefinida com sucesso.');

        $user->refresh();

        $this->assertTrue(Hash::check('NovaSenha123', $user->password));
    }

    public function test_user_can_reset_password_with_frontend_field_names(): void
    {
        $user = $this->createUserWithRole('doador', [
            'email' => 'campos-frontend@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        $forgotResponse = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $codigo = $forgotResponse->json('codeForDev');

        $resetResponse = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'code' => $codigo,
            'newPassword' => 'NovaSenha123',
            'confirmPassword' => 'NovaSenha123',
        ]);

        $resetResponse->assertOk()
            ->assertJsonPath('message', 'Senha redefinida com sucesso.');

        $user->refresh();

        $this->assertTrue(Hash::check('NovaSenha123', $user->password));
    }

    public function test_only_donors_can_request_password_reset_code(): void
    {
        $user = $this->createUserWithRole('funcionario', [
            'email' => 'funcionario@example.com',
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Apenas doadores cadastrados podem recuperar a senha.');

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }

    public function test_only_donors_can_reset_password(): void
    {
        $user = $this->createUserWithRole('funcionario', [
            'email' => 'bloqueado@example.com',
            'password' => Hash::make('senha-antiga'),
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('123456'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => '123456',
            'password' => 'NovaSenha123',
            'password_confirmation' => 'NovaSenha123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', 'Apenas doadores cadastrados podem recuperar a senha.');

        $user->refresh();

        $this->assertTrue(Hash::check('senha-antiga', $user->password));
    }

    private function createUserWithRole(string $roleName, array $attributes = []): User
    {
        $role = Role::firstOrCreate([
            'name' => $roleName,
            'guard_name' => 'api',
        ]);

        $user = User::factory()->create([
            ...$attributes,
            'role_id' => $role->id,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
