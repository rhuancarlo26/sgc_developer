<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoPmqaPonto extends Model
{
    use HasFactory;

    protected $table = 'servico_pmqa_pontos';
    protected $guarded = ['id', 'created_at'];

    public function vinculado()
    {
        return $this->hasOne(ServicoPmqaListaPonto::class, 'ponto_id');
    }

    public function campanhas()
    {
        return $this->belongsToMany(ServicoPmqaCampanha::class, 'servico_pmqa_campanha_pontos', 'ponto_id', 'campanha_id');
    }
}