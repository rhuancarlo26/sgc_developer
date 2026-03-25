<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoConOcorrSupressaoRelatorio extends Model
{
    use HasFactory;

    protected $table = 'supressao_relatorio';
    protected $guarded = ['id'];
}
