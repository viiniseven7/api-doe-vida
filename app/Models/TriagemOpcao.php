<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriagemOpcao extends Model
{
    protected $table = 'triagem_opcoes';

    protected $fillable = [
        'pergunta_id',
        'texto_opcao',
        'gera_inaptidao',
        'dias_inaptidao',
    ];

    protected $casts = [
        'gera_inaptidao' => 'boolean',
        'dias_inaptidao' => 'integer',
    ];

    public function pergunta()
    {
        return $this->belongsTo(TriagemPergunta::class, 'pergunta_id');
    }

    public function respostas()
    {
        return $this->hasMany(TriagemResposta::class, 'opcao_id');
    }

    public function ehDefinitiva(): bool
    {
        return $this->gera_inaptidao && $this->dias_inaptidao === 9999;
    }
}
