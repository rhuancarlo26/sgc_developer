<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicoMonitoraFaunaResultadoCampanha extends Model
{
    use HasFactory;

    protected $table = 'fauna_resultado_campanha';
    protected $guarded = ['id', 'created_at'];

    public function campanha()
    {
        return $this->hasOne(ServicoMonitoraFaunaResultadoCampanha::class, 'id_campanha');
    }
}
