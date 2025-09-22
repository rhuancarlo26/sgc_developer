<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services\EspeleoService;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Request\EspeleoSalvarCampanhaRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class EspeleoCampanhaController extends Controller
{
    protected $espeleoService;

    public function __construct(EspeleoService $espeleoService)
    {
        $this->espeleoService = $espeleoService;
    }

    public function salvarCampanha(EspeleoSalvarCampanhaRequest $request, $contrato, $produto)
    {
        if ($produto !== 'espeleologia') {
            Log::error('Produto inválido', ['produto' => $produto]);
            abort(404, 'Produto inválido');
        }

        $data = $request->validated();
        Log::info('Dados recebidos para salvar campanha', ['data' => $data, 'contrato' => $contrato, 'campanhaId' => $request->id]);

        try {
            $campanha = $this->espeleoService->salvarCampanha($data, $contrato, $request->id);
            Log::info('Campanha salva com sucesso', ['campanha_id' => $campanha->id]);
            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, $produto]));
        } catch (\Exception $e) {
            Log::error('Erro ao salvar campanha', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}