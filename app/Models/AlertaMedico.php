<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlertaMedico extends Model
{
    use SoftDeletes;

    protected $table = 'alertas_medicos';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';
    const DELETED_AT = 'deletado_em';

    protected $fillable = [
        'user_id',
        'hemocentro_id',
        'criado_por',
        'tipo_alerta',
        'status',
        'notificacao_doador',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    public function doador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hemocentro()
    {
        return $this->belongsTo(Hemocentro::class, 'hemocentro_id');
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
