<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\app\Utils\ConfigucacaoParecer;
use App\Models\AfugentFaunaConfigABIOModel;
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
        $licencaABIO = Licenca::where('tipo', 12)->with('tipo')->get();
        $licencaVinculadas = AfugentFaunaConfigABIOModel::with(['licenca.tipo', 'licenca.user'])
            ->where('id_servico', $servico->id)->get();

        return Inertia::render('Servico/AfugentamentoResgateFauna/Configuracao/VincularABIO', [
            'contrato'    => $contrato,
            'servico'     => $servico->load(['tipo']),
            'licencaABIO' => $licencaABIO,
            'licencaVinculadas' => $licencaVinculadas,
            ...$this->configucacaoParecer->get($servico->id)

        ]);
    }
}
