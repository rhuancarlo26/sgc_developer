<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller;

use App\Models\AfugentFaunaConfigABIOModel;
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
        $licencaABIO = Licenca::where('tipo_id', 12)->with('tipo')->get();
        $licencaVinculadas = AfugentFaunaConfigABIOModel::with(['licenca.tipo', 'licenca.user'])->get();

        return Inertia::render('Servico/AfugentamentoResgateFauna/Configuracao/VincularABIO', [
            'contrato'  => $contrato,
            'servico'   => $servico->load(['tipo']),
            'licencaABIO' => $licencaABIO,
            'licencaVinculadas' => $licencaVinculadas
            // ...$response
        ]);
    }
}
