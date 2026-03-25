<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Requests\VincularASVRequests;
use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Service\VincularASVService;
use App\Models\AfugentFaunaConfigASVModel;
use App\Shared\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Servicos;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class VincularASVController extends Controller
{
    public function __construct(private readonly VincularASVService $vincularASVService)
    {
    }

    public function index(Licenca $licenca, Servicos $servico)
    {
        try {
            $this->vincularASVService->create($licenca, $servico);
            return redirect()->back()->with('message',  [
                'type' => 'success',
                'content' =>  'Vinculação realizada com sucesso'
            ]);
        } catch (Throwable $th) {
            return redirect()->back()->with(
                'message',
                [
                    'type' =>  'error',
                    'content' => $th->getMessage()
                ]
            );
        }
    }
}
