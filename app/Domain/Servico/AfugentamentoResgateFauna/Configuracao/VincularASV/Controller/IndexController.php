<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\app\Utils\ConfigucacaoParecer;
use App\Models\AfugentFaunaConfigASVModel;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __construct(private readonly ConfigucacaoParecer $configucacaoParecer)
    {
    }

    public function index(Contrato $contrato, Servicos $servico): Response
    {
        $licencaASV = Licenca::where('tipo', 3)->with('tipo')->get();
        $licencaVinculadas = AfugentFaunaConfigASVModel::with(['licenca.tipo', 'licenca.user'])
            ->where('id_servico', $servico->id)->get();

        return Inertia::render('Servico/AfugentamentoResgateFauna/Configuracao/VincularASV', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'licencaASV' => $licencaASV,
            'licencaVinculadas' => $licencaVinculadas,
            ...$this->configucacaoParecer->get($servico->id)
            // ...$response
        ]);
    }
}
