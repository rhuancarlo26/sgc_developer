<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AfugentFaunaRelatorioModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_relatorio';

    protected $guarded = ['id', 'created_at'];

    public function getRelatorios($id_servico, $perPage = 10)
    {
        return $this->where('id_servico', $id_servico)
            ->paginate($perPage, ['id', 'id_resultado', 'nome_relatorio', 'sobre_relatorio', 'conclusao', 'fk_status', 'created_at'])
            ->through(function ($item) {
                $item->data_cadastro = $item->created_at->format('d/m/Y');
                return $item;
            });
    }

    public function servico()
    {
        return $this->belongsTo(Servicos::class, 'id_servico');
    }
}
