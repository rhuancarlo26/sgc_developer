<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\ResultadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResultadoController extends Controller
{
    protected $resultadoService;

    public function __construct(ResultadoService $resultadoService)
    {
        $this->resultadoService = $resultadoService;
    }

    public function upload(Request $request, $contrato, $produto)
    {
        if (!Auth::check()) {
            return back()->withErrors(['error' => 'Acesso negado.']);
        }

        try {
            $campanhaId = $request->campanha_id;
            $file = $request->file('planilha');
            $consideracoes = $request->consideracoes;

            $resultado = $this->resultadoService->salvarResultados(
                $contrato,
                $file,
                $campanhaId,
                $consideracoes
            );

            return back()->with('success', $resultado['message']);

        } catch (\Exception $e) {
            Log::error('Erro no upload dos resultados', ['erro' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
