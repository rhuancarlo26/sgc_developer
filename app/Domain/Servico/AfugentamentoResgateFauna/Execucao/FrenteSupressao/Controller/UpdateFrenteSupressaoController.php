<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Requests\CreateRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Service\FrenteSupressaoService;
use App\Models\AfugentFaunaExecFrenteModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateFrenteSupressaoController extends Controller
{
    public function __construct(private readonly FrenteSupressaoService $frenteSupressaoService)
    {
    }

    public function index(CreateRequest $request,  AfugentFaunaExecFrenteModel $frente)
    {
        $this->frenteSupressaoService->update($request->validated(), $frente);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Frente de supressão atualizada com sucesso'
        ]); 
    }
}