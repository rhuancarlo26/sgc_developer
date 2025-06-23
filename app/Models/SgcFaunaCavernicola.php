<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SgcFaunaCavernicola extends Model
{
    use SoftDeletes;

    protected $table = 'sgc_fauna_amostragem_cavernicola';
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['deleted_at'];
}