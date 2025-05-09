<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaConfigABIOModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_config_abio';

    protected $guarded = ['id', 'created_at'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class, 'id_licenca');
    }
}
