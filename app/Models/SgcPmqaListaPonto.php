<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcPmqaListaPonto extends Model
{
    use HasFactory;

    protected $table = 'sgc_pmqa_config_ponto_lista';
    protected $guarded = ['id', 'created_at'];


    public function ponto()
    {
        return $this->belongsTo(SgcPmqaPonto::class, 'ponto_id');
    }

    public function lista()
    {
        return $this->belongsTo(SgcPmqaParametroLista::class, 'lista_id');
    }

    public function pmqa()
    {
        return $this->belongsTo(SgcPmqa::class, 'pmqa_id');
    }
}
