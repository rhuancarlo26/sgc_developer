<?php

namespace App\Domain\Contrato\Contratada\app\Services;

use App\Models\Licenca;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use Illuminate\Support\Facades\Cache;

class DadosGeraisService extends BaseModelService
{
    protected string $modelClass = Licenca::class;

    public function index($contrato): array
    {
        $ufs = Cache::rememberForever('ufs', fn() => Uf::all());
        $rodovias = Cache::rememberForever('rodovias', fn() => Rodovia::all());
        $numero_licencas = $this->modelClass::all(['id', 'numero_licenca']);

        if ($contrato) {
            $contrato->load([
                'anexos',
                'trechos',
                'trechos.uf',
                'trechos.rodovia',
                'historico',
                'licenciamentos',
                'licenciamentos.requerimentos',
                'licenciamentos.tipo_rel',
                'licenciamento_observacoes',
                'servicos.supervisao_lotes.rodovia',
                'servicos.supervisao_lotes.uf'
            ]);
        }

        return [
            'contrato' => $contrato,
            'numero_licencas' => $numero_licencas,
            'ufs' => $ufs,
            'rodovias' => $rodovias
        ];
    }
}
