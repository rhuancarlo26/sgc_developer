<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfugentFaunaTipoRegistroModel extends Model
{
    use HasFactory;

    protected $table = 'afugent_fauna_tipo_registro';

    protected $guarded = ['id', 'created_at'];

}
