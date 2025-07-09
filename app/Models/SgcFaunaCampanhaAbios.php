<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SgcFaunaCampanhaAbios extends Model
{
    protected $table = 'sgc_fauna_campanha_abios';
    protected $guarded = ['id'];
    public $timestamps = true;

    protected $fillable = [
        'contrato_id',
        'campanha_id',
        'n_abio',
    ];

    public function abio()
    {
        return $this->belongsTo(Licenca::class, 'n_abio', 'id');
    }

    public function campanha()
    {
        return $this->belongsTo(SgcFaunaCampanha::class, 'campanha_id', 'id');
    }
}