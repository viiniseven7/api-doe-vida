<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTipoSangueHistorico extends Model
{
    protected $table = 'user_tipo_sangue_historico';

    const CREATED_AT = 'alterado_em';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'tipo_sangue_anterior',
        'tipo_sangue_novo',
        'alterado_por',
        'categoria_motivo',
    ];

    protected $casts = [
        'alterado_em' => 'datetime',
    ];

    public function doador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function alteradoPor()
    {
        return $this->belongsTo(User::class, 'alterado_por');
    }
}
