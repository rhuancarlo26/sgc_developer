<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaExecRegistroImagemModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_exec_registro_imagem';

    protected $guarded = ['id', 'created_at'];


}
