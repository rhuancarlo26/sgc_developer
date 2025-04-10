<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Resultado\Service\ResultadoService;
use App\Models\AfugentFaunaResultadoModel;
use App\Shared\Http\Controllers\Controller;

class DestroyResultadoController extends Controller
{
    public function __construct(private readonly ResultadoService $resultadoService)
    {
    }

    public function index(AfugentFaunaResultadoModel $resultado)
    {
        $this->resultadoService->delete($resultado);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Resultado deletado com sucesso'
        ]);
    }
}