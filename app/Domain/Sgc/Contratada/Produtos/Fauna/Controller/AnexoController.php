<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\AnexoService;
use App\Models\SgcFaunaAnexo;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnexoController extends Controller
{
    protected $anexoService;

    public function __construct(AnexoService $anexoService)
    {
        $this->anexoService = $anexoService;
    }

    public function destroyAnexo($contrato, $produto, $campanhaId, $anexoId)
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }
        try {
            $this->anexoService->excluirAnexo($contrato, $campanhaId, $anexoId);
            return redirect()->back()->with('success', 'Anexo excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('AnexoController: Erro ao excluir anexo', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanhaId,
                'anexo_id' => $anexoId,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao excluir anexo: ' . $e->getMessage()]);
        }
    }
}