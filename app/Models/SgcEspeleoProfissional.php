<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcEspeleoProfissional extends Model
{
    protected $table = 'sgc_espeleo_profissionais';
    protected $fillable = [
        'id_contrato', 'profissional', 'formacao', 'telefone', 'cpf', 'email',
        'curriculum_lattes', 'funcao', 'ctf', 'validade', 'conselho_de_classe',
        'numero_de_registro', 'status', 'observacao'
    ];
}