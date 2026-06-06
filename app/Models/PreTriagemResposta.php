<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreTriagemResposta extends Model
{
    protected $table = 'pre_triagem_respostas';

    const CREATED_AT = 'respondido_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'pergunta_id',
        'opcao_id',
        'resultado_geral',
    ];

    protected $casts = [
        'respondido_em' => 'datetime',
    ];

    public function doador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pergunta()
    {
        return $this->belongsTo(TriagemPergunta::class, 'pergunta_id');
    }

    public function opcao()
    {
        return $this->belongsTo(TriagemOpcao::class, 'opcao_id');
    }
}
