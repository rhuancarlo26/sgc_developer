<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcDavProfissionais extends Model
{
    use HasFactory;

    protected $table = 'sgc_dav_profissionais';
    protected $guarded = ['id', 'created_at'];
}
