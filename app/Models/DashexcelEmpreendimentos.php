<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashexcelEmpreendimentos extends Model
{
    use HasFactory;

    protected $table = 'sgcvw_empreendimentos';
    protected $guarded = ['id', 'created_at'];
}
