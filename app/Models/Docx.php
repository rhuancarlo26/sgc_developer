<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docx extends Model
{
    use HasFactory;

    protected $table = 'relatorio_upload';
    protected $guarded = ['id', 'created_at'];
}