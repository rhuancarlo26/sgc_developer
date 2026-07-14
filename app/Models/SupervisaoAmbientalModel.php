<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupervisaoAmbientalModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'supervisao_exec_ocorrencia';

    protected $guarded = ['id', 'created_at'];

    
}
