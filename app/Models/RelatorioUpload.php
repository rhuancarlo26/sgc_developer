<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioUpload extends Model
{
    use HasFactory;

    protected $table = 'relatorio_upload';
    protected $guarded = ['id', 'created_at'];

    public function SgcRelatorioCoordenacao()
    {
        return $this->belongsTo(SgcRelatorioCoordenacao::class, 'item_id', 'id_item');
    }

}

