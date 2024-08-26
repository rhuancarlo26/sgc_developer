<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Service\VincularABIOService;
use App\Shared\Http\Controllers\Controller;
use App\Models\AfugentFaunaConfigABIOModel;
use App\Models\Licenca;
use App\Models\Servicos;
use Throwable;

class VincularABIOController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIOService)
    {
    }

    public function index(Licenca $licenca, Servicos $servico)
    {
        try {
            $this->vincularABIOService->create($licenca, $servico);
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