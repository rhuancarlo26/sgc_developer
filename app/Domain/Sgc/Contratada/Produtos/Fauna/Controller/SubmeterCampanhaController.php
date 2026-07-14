<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\SgcFaunaCampanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubmeterCampanhaController extends Controller
{
    /**
     * Rota: POST /{contrato}/produtos/{produto}/rascunho/{campanhaId}/submeter
     *
     * Muda status de 'Em elaboração' para 'Em análise'.
     * Só submete se todas as etapas obrigatórias foram salvas.
     * 
     * Retorna JSON se for requisição AJAX, redirect se for normal.
     */
    public function __invoke(Request $request, $contrato, $produto, $campanhaId)
    {
        try {
            // ────────────────────────────────────────────────────────────────
            // VALIDAÇÕES
            // ────────────────────────────────────────────────────────────────
            
            $campanha = SgcFaunaCampanha::where('id', $campanhaId)
                ->where('id_contrato', $contrato)
                ->firstOrFail();

            // Validar permissão (perfis_id === 3 é fiscal? ou === 2?)
            if (Auth::user()->perfis_id === 3) {
                $message = 'Fiscais não podem submeter campanhas.';
                
                if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                    return response()->json(['message' => $message, 'success' => false], 403);
                }
                return back()->withErrors(['error' => $message]);
            }

            // Validar status
            if ($campanha->status !== 'Em elaboração') {
                $message = 'Apenas rascunhos podem ser submetidos.';
                
                if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                    return response()->json(['message' => $message, 'success' => false], 400);
                }
                return back()->withErrors(['error' => $message]);
            }

            // Validar se ao menos apresentação foi preenchida
            if (empty($campanha->cod_emp) || empty($campanha->subproduto)) {
                $message = 'Preencha ao menos a aba de Apresentação antes de submeter.';
                
                if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                    return response()->json(['message' => $message, 'success' => false], 400);
                }
                return back()->withErrors(['error' => $message]);
            }

            // ────────────────────────────────────────────────────────────────
            // SUBMETER CAMPANHA
            // ────────────────────────────────────────────────────────────────
            
            $campanha->update([
                'status'      => 'Em análise',
                'etapa_atual' => null, // limpa — não é mais rascunho
            ]);

            Log::info('SubmeterCampanha: campanha submetida para análise', [
                'campanha_id' => $campanha->id,
                'user_id'     => Auth::id(),
                'contrato_id' => $contrato,
            ]);

            $successMessage = 'Campanha submetida para análise com sucesso!';

            // ────────────────────────────────────────────────────────────────
            // RETORNAR JSON OU REDIRECT
            // ────────────────────────────────────────────────────────────────
            
            if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                return response()->json([
                    'message' => $successMessage,
                    'success' => true,
                    'campanha_id' => $campanha->id,
                    'status' => $campanha->status,
                ], 200);
            }

            // Redirect normal (para navegação tradicional)
            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', $successMessage);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('SubmeterCampanha: campanha não encontrada', [
                'campanha_id' => $campanhaId,
                'contrato_id' => $contrato,
            ]);

            $message = 'Campanha não encontrada.';
            
            if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                return response()->json(['message' => $message, 'success' => false], 404);
            }
            return back()->withErrors(['error' => $message]);

        } catch (\Exception $e) {
            Log::error('SubmeterCampanha: erro ao submeter', [
                'campanha_id' => $campanhaId,
                'contrato_id' => $contrato,
                'erro' => $e->getMessage(),
            ]);

            $message = 'Erro ao submeter campanha: ' . $e->getMessage();
            
            if ($request->expectsJson() || $request->header('X-Inertia') === 'false') {
                return response()->json(['message' => $message, 'success' => false], 500);
            }
            return back()->withErrors(['error' => $message]);
        }
    }
}