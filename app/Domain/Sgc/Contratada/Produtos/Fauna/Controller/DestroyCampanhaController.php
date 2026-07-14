<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DestroyCampanhaController extends Controller
{
    public function __invoke($contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::where('id', $campanhaId)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        if ($campanha->status !== 'Em elaboração') {
            return back()->withErrors(['error' => 'Apenas campanhas em elaboração podem ser excluídas.']);
        }

        try {
            DB::beginTransaction();

            // Deleta relacionamentos
            $campanha->abios()->delete();
            $campanha->profissionais()->delete();
            $campanha->modulos_amostrais()->delete();
            $campanha->pontos_quelo_crocod()->delete();
            $campanha->pontos_cavernicola()->delete();
            $campanha->metodologias()->delete();
            $campanha->resultados()->delete();
            $campanha->resultadosTerrestre()->delete();
            $campanha->resultadosAquatica()->delete();
            $campanha->resultadosCavernicola()->delete();
            $campanha->resultados_consideracoes()->delete();
            $campanha->anexos()->delete();
            $campanha->delete();

            DB::commit();

            Log::info('DestroyCampanha: campanha excluída', [
                'campanha_id' => $campanhaId,
                'user_id'     => Auth::id(),
            ]);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha excluída com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('DestroyCampanha: erro ao excluir', [
                'campanha_id' => $campanhaId,
                'erro'        => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Erro ao excluir campanha: ' . $e->getMessage()]);
        }
    }
}
