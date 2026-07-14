<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SgcvwSubprodutos extends Model
{
    use HasFactory;
    protected $table = 'sgcvw_subprodutos';
    protected $guarded = ['id', 'created_at'];
    public function changelogs()
    {
        return $this->hasMany(ChangeLog::class, 'record_id')
            ->where('table_name', $this->table)
            ->with('user')
            ->orderBy('created_at', 'desc');
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
