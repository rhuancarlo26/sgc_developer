<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Requests\StoreRequest;
use App\Models\AfugentFaunaExecRegistroModel;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Store;

class UpdateRegistroController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService)
    {
    }

    public function index(StoreRequest $request, AfugentFaunaExecRegistroModel $registro)
    {
        $this->registrosService->update($request->validated(), $registro);
        return redirect()->back()->with('message',  [
            'type' => 'info',
            'content' =>  'Registro atualizado com sucesso'
        ]);
    }
}