<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularABIO\Service\VincularABIOService;
use App\Models\AfugentFaunaConfigABIOModel;
use App\Shared\Http\Controllers\Controller;

class DestroyABIOController extends Controller
{
    public function __construct(private readonly VincularABIOService $vincularABIOService)
    {
    }

    public function index(AfugentFaunaConfigABIOModel $licenca)
    {
        $this->vincularABIOService->delete($licenca);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Vinculação removida com sucesso'
        ]);
    }
}