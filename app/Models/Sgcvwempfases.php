<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sgcvwempfases extends Model
{
    use HasFactory;
    protected $fillable = [
        'fase',
        'status',
        'periodo',
        'empreendimento_id',
    ];
}
