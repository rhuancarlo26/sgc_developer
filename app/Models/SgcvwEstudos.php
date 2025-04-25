<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwEstudos extends Model
{

    use HasFactory;
    protected $table = 'sgcvw_estudos';
    protected $guarded = ['id', 'created_at'];
    
}
