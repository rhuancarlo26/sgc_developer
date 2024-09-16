<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtFaunaResultado extends Model
{
    use SoftDeletes;

    protected $table = 'at_fauna_resultado';

    protected $guarded = ['id'];
}
