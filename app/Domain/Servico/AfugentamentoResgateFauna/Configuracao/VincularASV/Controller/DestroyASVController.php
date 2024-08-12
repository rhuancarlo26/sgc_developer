<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Configuracao\VincularASV\Service\VincularASVService;
use App\Models\AfugentFaunaConfigASVModel;
use App\Shared\Http\Controllers\Controller;

class DestroyASVController extends Controller
{
    public function __construct(private readonly VincularASVService $vincularASVService)
    {
    }

    public function index(AfugentFaunaConfigASVModel $licenca)
    {
        $this->vincularASVService->delete($licenca);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Vinculação removida com sucesso'
        ]);
    }
}
