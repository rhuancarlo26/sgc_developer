<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoVeiculo extends Model
{
    use HasFactory;

    protected $table = 'servico_veiculo';
    protected $guarded = ['id', 'created_at'];
    public $timestamps = false;
}
