<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SgcPmqaCampanha extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_campanhas';
    protected $guarded = ['id', 'created_at'];

    public function pontos(): BelongsToMany
    {
        return $this->belongsToMany(
            SgcPmqaPonto::class,
            'sgc_pmqa_campanhas_pontos',
            'sgc_pmqa_campanha_id',  // Foreign key da tabela atual (campanhas) na pivot
            'ponto_id'      // Foreign key da tabela relacionada (pontos) na pivot
        );
    }

    public function campanha_pontos()
    {
        return $this->hasMany(SgcPmqaCampanhasPonto::class, 'sgc_pmqa_campanha_id');
    }
}
