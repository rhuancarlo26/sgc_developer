<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Service\FrenteSupressaoService;
use App\Models\AfugentFaunaExecFrenteModel;
use App\Shared\Http\Controllers\Controller;

class DestroyFrenteSupressaoController extends Controller
{
    public function __construct(private readonly FrenteSupressaoService $frenteSupressaoService)
    {
    }

    public function index(AfugentFaunaExecFrenteModel $frente)
    {
        $this->frenteSupressaoService->delete($frente);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Frente de supressão deletada com sucesso'
        ]);
    }
}
