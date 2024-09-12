<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicoPassagemFaunaExecCampanha extends Model
{
    use HasFactory;

    protected $table = 'passagem_fauna_exec_campanhas';
    protected $guarded = ['id'];

    public function abios(): HasMany
    {
        return $this->hasMany(ServicoPassagemFaunaExecCampanhasAbio::class, 'id_campanha');
    }

    public function rhs()
    {
        return $this->hasMany(ServicoPassagemFaunaExecCampanhasRh::class, 'id_campanha');
    }

    public function rets()
    {
        return $this->hasMany(ServicoPassagemFaunaExecCampanhasRet::class, 'id_campanha');
    }
}
