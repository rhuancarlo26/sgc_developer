<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupressaoRelatorio extends Model
{
    use SoftDeletes;

    protected $table = 'supressao_relatorio';

    protected $guarded = ['id'];
}
