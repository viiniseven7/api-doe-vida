<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriagemSinaisVitais extends Model
{
    protected $table = 'triagem_sinais_vitais';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'triagem_id',
        'peso',
        'pressao_sistolica',
        'pressao_diastolica',
        'temperatura',
        'frequencia_cardiaca',
        'hemoglobina',
        'hematocrito',
    ];

    protected $casts = [
        'peso' => 'float',
        'temperatura' => 'float',
        'hemoglobina' => 'float',
        'hematocrito' => 'float',
        'pressao_sistolica' => 'integer',
        'pressao_diastolica' => 'integer',
        'frequencia_cardiaca' => 'integer',
        'criado_em' => 'datetime',
    ];

    public function triagem()
    {
        return $this->belongsTo(Triagem::class, 'triagem_id');
    }

    public function pressaoNormal(): bool
    {
        return $this->pressao_sistolica >= 90
            && $this->pressao_sistolica <= 160
            && $this->pressao_diastolica >= 60
            && $this->pressao_diastolica <= 100;
    }

    public function temperaturaNormal(): bool
    {
        return $this->temperatura !== null && $this->temperatura <= 37.0;
    }

    public function hemoglobinaNormal(string $sexo): bool
    {
        $minimo = $sexo === 'M' ? 13.0 : 12.5;

        return $this->hemoglobina !== null && $this->hemoglobina >= $minimo;
    }
}
