<?php

namespace App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Controller;

use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Service\RegistrosService;
use App\Domain\Servico\AfugentamentoResgateFauna\Execucao\Registros\Requests\StoreRequest;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Contracts\Cache\Store;

class CreateRegistroController extends Controller
{
    public function __construct(private readonly RegistrosService $registrosService) {}

    public function index(StoreRequest $request, Servicos $servico)
    {
        $this->registrosService->create($request->validated(), $servico);
        return redirect()->back()->with('message',  [
            'type' => 'success',
            'content' =>  'Registro criado com sucesso'
        ]);
    }
}
