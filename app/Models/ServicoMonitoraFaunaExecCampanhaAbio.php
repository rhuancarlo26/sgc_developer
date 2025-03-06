<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServicoMonitoraFaunaExecCampanhaAbio extends Model
{
    use HasFactory;

    protected $table = 'fauna_exec_campanhas_abio';
    protected $guarded = ['id'];

    public function abio(): BelongsTo
    {
        return $this->belongsTo(ServicoMonitoraFaunaConfigAbio::class, 'id_abio');
    }
}
