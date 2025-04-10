<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcStatusRelatorio extends Model
{
    use HasFactory;

    protected $table = 'sgc_status_relatorio';
    protected $guarded = ['id', 'created_at'];

    public function relatorios()
    {
        return $this->hasMany(SgcRelatorioCoordenacao::class, 'status', 'id_status');
    }

}

