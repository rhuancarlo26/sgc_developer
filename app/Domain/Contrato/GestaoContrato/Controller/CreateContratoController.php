<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Models\Contrato;
use App\Models\ContratoSituacao;
use App\Models\ContratoTipo;
use App\Models\Rodovia;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CreateContratoController extends Controller
{
    public function create(Contrato|null $contrato)
    {
        $ufs = Cache::rememberForever('ufs', fn () => Uf::all());
        $rodovias = Cache::rememberForever('rodovias', fn () => Rodovia::all());
        $tipos = Cache::rememberForever('contrato_tipos', fn () => ContratoTipo::all());
        $situacoes = Cache::rememberForever('contrato_situacoes', fn () => ContratoSituacao::all());

        if ($contrato) {
            $contrato->load([
                'situacao',
                'tipo',
                'trechos',
                'trechos.uf',
                'trechos.rodovia'
            ]);
        }

        return Inertia::render('Contrato/GestaoContrato/Form', [
            'ufs' => $ufs,
            'rodovias' => $rodovias,
            'tipos' => $tipos,
            'situacoes' => $situacoes,
            'contrato' => $contrato
        ]);
    }
}