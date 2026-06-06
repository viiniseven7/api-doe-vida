<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriagemAptidao extends Model
{
    protected $table = 'triagem_aptidao';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'triagem_id',
        'resultado',
        'categoria_inaptidao',
        'observacoes_internas',
        'notificacao_doador',
        'valido_ate',
    ];

    protected $casts = [
        'valido_ate' => 'date',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    public function triagem()
    {
        return $this->belongsTo(Triagem::class, 'triagem_id');
    }

    public function restricaoAtiva(): bool
    {
        if ($this->resultado === 'inapto_definitivo') {
            return true;
        }

        if ($this->resultado === 'inapto_temporario' && $this->valido_ate) {
            return now()->lessThanOrEqualTo($this->valido_ate);
        }

        return false;
    }

    public function getCategoriaLabelAttribute(): ?string
    {
        $labels = [
            'sinais_vitais_fora_do_padrao' => 'Sinais vitais fora do padrão',
            'intervalo_minimo_nao_cumprido' => 'Intervalo mínimo entre doações não cumprido',
            'medicamento_incompativel' => 'Uso de medicamento incompatível',
            'cirurgia_recente' => 'Procedimento cirúrgico recente',
            'viagem_area_de_risco' => 'Viagem para área de risco',
            'comportamento_de_risco' => 'Comportamento de risco recente',
            'condicao_clinica_na_triagem' => 'Condição clínica identificada na triagem',
            'resultado_sorologico_alterado' => 'Resultado sorológico alterado',
            'outro' => 'Outro motivo',
        ];

        return $labels[$this->categoria_inaptidao] ?? null;
    }
}
