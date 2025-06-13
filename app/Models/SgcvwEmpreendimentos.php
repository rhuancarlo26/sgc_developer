<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwEmpreendimentos extends Model
{
    use HasFactory;
    protected $appends = ['data_ultima_alteracao'];

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
}
