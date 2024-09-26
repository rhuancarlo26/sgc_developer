<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaRelatorioAnexosModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_relatorio_anexos';

    protected $guarded = ['id', 'created_at'];
}
