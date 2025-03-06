<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Requests\CreateRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\FrenteSupressao\Service\FrenteSupressaoService;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Throwable;

class CreateFrenteSupressaoController extends Controller
{
    public function __construct(private readonly FrenteSupressaoService $frenteSupressaoService)
    {
    }

    public function index(CreateRequest $request, Servicos $servico)
    {
        try {
            $this->frenteSupressaoService->create($request->validated(), $servico);
            return redirect()->back()->with('message',  [
                'type' => 'success',
                'content' =>  'Frente de supressão criada com sucesso'
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