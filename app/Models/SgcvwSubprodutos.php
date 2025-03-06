<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwSubprodutos extends Model
{
    use HasFactory;
    protected $table = 'sgcvw_subprodutos';
    protected $guarded = ['id', 'created_at'];

}
