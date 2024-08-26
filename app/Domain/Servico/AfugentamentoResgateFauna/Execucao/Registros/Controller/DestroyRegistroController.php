<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Shared\Http\Controllers\Controller;

class DestroyRegistroController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(AfugentFaunaExecRegistroModel $registro)
    {
        $this->registrosService->delete($registro);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Registro deletado com sucesso'
        ]);
    }
}