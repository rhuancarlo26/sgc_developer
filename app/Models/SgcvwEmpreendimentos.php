<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SgcvwEmpreendimentos extends Model
{
    use HasFactory;

    protected $table = 'sgcvw_empreendimentos';
    protected $guarded = ['id', 'created_at'];

    public function estudos()
    {
        return SgcvwEstudos::where('cod_emp', $this->cod_emp)->get();
    }

    public function changelogs()
    {
        return $this->hasMany(ChangeLog::class, 'record_id')
            ->where('table_name', 'sgcvw_empreendimentos')
            ->with('user')
            ->orderBy('created_at', 'desc');
    }
    public function getContEstudosAttribute()
    {
        return $this->estudos()->count();
    }
    public function getDataUltimaAlteracaoAttribute()
    {
        $logs = $this->changelogs;

        // Se for array de logs, pega a data mais recente
        if (is_array($logs) && !empty($logs)) {
            $datas = array_column($logs, 'created_at');
            return max($datas);
        }

        return null;
    }

    public function paipas(): HasMany
    {
        return $this->hasMany(SgcPatrimonioPaipa::class, 'empreendimento_id', 'id');
    }
}
