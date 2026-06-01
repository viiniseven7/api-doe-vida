<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campanha extends Model
{
    use SoftDeletes;

    protected $table = 'campanhas';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'deletado_em';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'descricao',
        'tipo_sangue',
        'hemocentro_id',
        'data_publi',
        'data_expiracao',
        'status',
        'criado_por',
        'total_disparado',
        'total_aberto',
    ];

    protected $casts = [
        'status'          => 'boolean',
        'data_publi'      => 'datetime',
        'data_expiracao'  => 'datetime',
        'criado_em'       => 'datetime',
        'atualizado_em'   => 'datetime',
        'total_disparado' => 'integer',
        'total_aberto'    => 'integer',
    ];

    public function hemocentro()
    {
        return $this->belongsTo(Hemocentro::class, 'hemocentro_id');
    }

    public function criador()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
