<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaAnexo extends Model
{
    protected $table = 'sgc_fauna_anexos';
    protected $fillable = ['id_contrato', 'id_campanha', 'tipo', 'nome_arquivo', 'caminho'];
}