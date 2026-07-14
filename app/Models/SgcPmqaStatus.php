<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaStatus extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_status';
    protected $guarded = ['id', 'created_at'];
}
