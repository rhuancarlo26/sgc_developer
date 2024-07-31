<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicoLicencaCondicionante extends Model
{
    use HasFactory;

    protected $table = 'servico_licenca_condicionantes';
    protected $guarded = ['id', 'created_at'];

public function licenca(): BelongsTo {
    return $this->belongsTo(Licenca::class, 'licenca_id');
}

public function condicionante(): BelongsTo {
    return $this->belongsTo(LicencaCondicionante::class, 'condicionante_id');
}

    public function getLicencaCondicionanteServico($id)
    {
        return $this
            ->select([
                'servico_licenca_condicionante.id',
                'servico_licenca_condicionante.vigente',
                'licencas.chave',
                'licencas.numero_licenca',
                'condicionantes.numero',
                'condicionantes.titulo_condicionante',
                'condicionantes.descricao'
            ])
            ->join('licencas', 'licencas.id = servico_licenca_condicionante.id_licenca')
            ->join('condicionantes', 'condicionantes.id = servico_licenca_condicionante.id_condicionante')
            ->where('id_servico', $id)
            ->findAll();
    }
}