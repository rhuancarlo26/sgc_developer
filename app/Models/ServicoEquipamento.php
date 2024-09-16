<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoEquipamento extends Model
{
    use HasFactory;

    protected $table = 'servico_equipamento';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
