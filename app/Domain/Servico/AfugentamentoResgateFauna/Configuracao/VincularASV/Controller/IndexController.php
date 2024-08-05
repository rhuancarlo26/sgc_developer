<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller;

use App\Models\AfugentFaunaConfigASVModel;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct()
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $licencaASV = Licenca::where('tipo_id', 3)->with('tipo')->get();
        $licencaVinculadas = AfugentFaunaConfigASVModel::with(['licenca.tipo', 'licenca.user'])->get();

        return Inertia::render('Servico/AfugentamentoResgateFauna/Configuracao/VincularASV', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'licencaASV' => $licencaASV,
            'licencaVinculadas' => $licencaVinculadas
            // ...$response
        ]);
    }
}
