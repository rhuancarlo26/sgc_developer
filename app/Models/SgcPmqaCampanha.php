<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaCampanha extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_campanhas';
    protected $guarded = ['id', 'created_at'];

    public function pontos()
    {
        return $this->belongsToMany(
            related: SgcPmqaPonto::class,
        );
    }

    public function campanha_pontos()
    {
        return $this->hasMany(SgcPmqaCampanhasPonto::class, 'sgc_pmqa_campanha_id');
    }
}
