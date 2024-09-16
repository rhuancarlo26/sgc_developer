<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Requests\StoreRequest;
use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\ResultadoService;
use App\Models\AfugentFaunaResultadoModel;
use App\Shared\Http\Controllers\Controller;

class UpdateResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }
    
    public function index(StoreRequest $request, AfugentFaunaResultadoModel $resultado)
    {
        $this->resultadoService->update($request->validated(), $resultado);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Resultado atualizado com sucesso'
        ]);
    }

}