<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcRelatorioUpload extends Model
{
    use HasFactory;

    protected $table = 'sgc_upload';
    protected $guarded = ['id', 'created_at'];

    public function SgcRelatorioCoordenacao()
    {
        return $this->belongsTo(SgcRelatorioCoordenacao::class, 'item_id', 'id_item');
    }
    

}

