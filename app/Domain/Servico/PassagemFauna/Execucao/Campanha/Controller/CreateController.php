<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Campanha\Controller;

use App\Domain\Servico\PassagemFauna\Execucao\Campanha\Services\CampanhaService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CreateController extends Controller
{
    public function __construct(private readonly CampanhaService $campanhaService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaExecCampanha $campanha): Response
    {
        $response = $this->campanhaService->create($servico);

        return Inertia::render('Servico/PassagemFauna/Execucao/Campanha/Form', [
            'contrato' => $contrato,
            'servico' => $servico->load([
                'tipo',
                'parecer_passagem_fauna',
                'rhs',
                'passagem_fauna_abios.licenca',
                'passagem_fauna_abios.abio_ret'
            ]),
            'campanha' => $campanha->load([
                'abios.abio.licenca',
                'rhs.servico_rh.rh',
                'rets.ret.abio.licenca'
            ]),
            ...$response
        ]);
    }
}
