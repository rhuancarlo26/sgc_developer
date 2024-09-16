<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtMalhaViariaShapefile extends Model
{
    use SoftDeletes;

    protected $table = 'at_malha_viaria_shapefile';

    protected $guarded = ['id'];
}
