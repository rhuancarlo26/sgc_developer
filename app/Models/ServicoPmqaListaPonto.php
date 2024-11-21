<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaListaPonto extends Model
{
    use HasFactory;

    protected $table = 'config_vinculacao';
    protected $guarded = ['id', 'created_at'];
}
