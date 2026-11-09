<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaComentarios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DestroyCampanhaController extends Controller
{
    public function __invoke($contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::where('id', $campanhaId)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        $isMaster = Auth::user()?->hasRole('Super Admin') ?? false;

        if (!$isMaster && $campanha->status !== 'Em elaboração') {
            return back()->withErrors(['error' => 'Apenas campanhas em elaboração podem ser excluídas.']);
        }

        try {
            DB::beginTransaction();

            $arquivos = $campanha->anexos()->pluck('caminho');
            $arquivos->push($campanha->planilha_atropelamento);

            $entrega = $campanha->entregaSimplificada()
                ->where('id_contrato', $contrato)->first();
            if ($entrega) {
                $arquivos = $arquivos->merge($entrega->arquivos()->pluck('caminho_arquivo'))
                    ->merge($entrega->planilhasFauna()->pluck('caminho_arquivo'));
                $arquivos->push($entrega->planilha_caminho);
                $entrega->arquivos()->delete();
                $entrega->planilhasFauna()->delete();
                $entrega->analises()->delete();
                $entrega->delete();
            }

            // Remove dados exclusivos da campanha; preserva profissionais, licencas e modelos.
            $campanha->analises()->delete();
            $campanha->atropelamento_campanhas()->delete();
            SgcFaunaComentarios::where('id_contrato', $contrato)
                ->where('campanha_id', $campanhaId)->delete();

            // Deleta relacionamentos
            $campanha->abios()->delete();
            $campanha->profissionais()->delete();
            $campanha->modulos_amostrais()->withTrashed()->forceDelete();
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

            try {
                $caminhos = $arquivos->filter()->unique()->values()->all();
                if ($caminhos && !Storage::disk('public')->delete($caminhos)) {
                    Log::warning('DestroyCampanha: alguns arquivos nao foram removidos', ['campanha_id' => $campanhaId]);
                }
            } catch (\Throwable $e) {
                Log::warning('DestroyCampanha: falha na limpeza dos arquivos', [
                    'campanha_id' => $campanhaId, 'erro' => $e->getMessage(),
                ]);
            }


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
