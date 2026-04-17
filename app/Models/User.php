<?php

namespace App\Models; // 🔥 TEM QUE SER O PRIMEIRO

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// 🔥 IMPORTANTE
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'tipo_sang', 
        'sexo', 
        'data_nasc', 
        'cpf', 
        'telefone', 
        'cep', 
        'rua', 
        'bairro', 
        'cidade', 
        'complemento', 
        'numero', 
        'uf', 
        'status', 
        'role_id', 
        'hemocentro_id', 
        
        'responsavel_nome',
        'responsavel_cpf',
        'responsavel_telefone',
        'responsavel_data_nasc',
        'criado_por'
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}