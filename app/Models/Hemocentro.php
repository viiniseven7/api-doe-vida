<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hemocentro extends Model
{ 
    protected $table = 'hemocentro';

    protected $fillable = [
        'nome', 'telefone','email','bairro', 'uf', 'endereco', 
        'cidade', 'numero', 'complemento', 'razao_social', 'cnpj', 
        'status_agendamento', 'status', 'criado_por'
    ];

    use SoftDeletes;
    protected $dates = ['deleted_at'];
    const DELETED_AT = 'deletado_em';

}
