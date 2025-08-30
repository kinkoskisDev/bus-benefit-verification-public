<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TesteModel extends Model
{
    protected $table = 'respondente';

    protected $fillable = [
        'id_respondente',
        'nome',
        'email',
        'cpf',
        'fk_questionario',
        'respostas',
        'respondido_em',
        'foi_lida',
        'baixado_por',
        'baixado_em',
    ];

    public $timestamps = false;
}
