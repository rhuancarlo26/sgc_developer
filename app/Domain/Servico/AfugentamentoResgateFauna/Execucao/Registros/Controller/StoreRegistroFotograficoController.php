<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreRegistroFotograficoController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(AfugentFaunaExecRegistroModel $registro, Request $request)
    {
        $response  = $this->registrosService->storeFile($registro, $request->file('documento'));
        
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Registro fotográfico adicionado com sucesso'
        ]);
    }
}
