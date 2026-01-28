<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaExecCampanha extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_campanhas';
    protected $guarded = ['id', 'created_at'];

    public function pontos()
    {
        return $this->belongsToMany(
            related: SgcPmqaPonto::class,
            table: 'sgc_pmqa_campanhas_pontos',
            foreignPivotKey: 'sgc_pmqa_campanha_id',
            relatedPivotKey: 'ponto_id'
        );
    }

    public function campanha_pontos()
    {
        return $this->hasMany(SgcPmqaCampanhasPonto::class, 'sgc_pmqa_campanha_id');
    }
}
