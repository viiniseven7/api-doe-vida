<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TriagemResposta extends Model
{
    protected $table = 'triagem_respostas';

    protected $fillable = [
        'triagem_id',
        'pergunta_id',
        'opcao_id',
    ];

    public function triagem()
    {
        return $this->belongsTo(Triagem::class, 'triagem_id');
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
