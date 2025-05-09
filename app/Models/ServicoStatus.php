<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoStatus extends Model
{
    use HasFactory;

    protected $table = 'servico_status';
    protected $guarded = ['id', 'created_at'];
}