<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaResultado extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_resultados';
    protected $guarded = ['id', 'created_at'];

    public function campanhas()
    {
        return $this->belongsToMany(ServicoPmqaCampanha::class, 'servico_pmqa_resultado_campanhas', 'resultado_id', 'campanha_id');
    }
}