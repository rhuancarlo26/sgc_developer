<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\ComentarioService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreComentarioRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    protected $comentarioService;

    public function __construct(ComentarioService $comentarioService)
    {
        $this->comentarioService = $comentarioService;
    }

    public function salvarComentario(StoreComentarioRequest $request, $contrato, $produto, $campanha)
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }
        try {
            $this->comentarioService->salvarComentario($contrato, $campanha, $request->validated());
            return redirect()->route('sgc.contratada.produtos.edit', [$contrato, $produto, $campanha])
                ->with('success', 'Comentário salvo com sucesso!');
        } catch (\Exception $e) {
            Log::error('ComentarioController: Erro ao salvar comentário', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar comentário: ' . $e->getMessage()]);
        }
    }

    public function destroyComentario($contrato, $produto, $campanha, $comentarioId)
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Você precisa estar autenticado.']);
        }
        try {
            $this->comentarioService->excluirComentario($contrato, $campanha, $comentarioId, Auth::id());
            return redirect()->route('sgc.contratada.produtos.edit', [$contrato, $produto, $campanha])
                ->with('success', 'Comentário excluído com sucesso!');
        } catch (\Exception $e) {
            Log::error('ComentarioController: Erro ao excluir comentário', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'comentario_id' => $comentarioId,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao excluir comentário: ' . $e->getMessage()]);
        }
    }
}