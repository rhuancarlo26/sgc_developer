<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\EtapaApresentacaoRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\EtapaMetodologiaRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\EtapaResultadosRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\EtapaAnexosRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\CampanhaService;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaCampanhaAbios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalvarEtapaCampanhaController extends Controller
{
    // Ordem das etapas — usada para avançar etapa_atual apenas para frente
    private const ORDEM_ETAPAS = [
        'apresentacao' => 1,
        'metodologia'  => 2,
        'resultados'   => 3,
        'anexos'       => 4,
    ];

    public function __construct(private CampanhaService $campanhaService) {}

    /**
     * Rota: POST /{contrato}/produtos/{produto}/rascunho/{campanhaId}/etapa/{etapa}
     *
     * {etapa} pode ser: apresentacao | metodologia | resultados | anexos
     */
    public function __invoke(Request $request, $contrato, $produto, $campanhaId, $etapa)
    {
        $campanha = SgcFaunaCampanha::where('id', $campanhaId)
            ->where('id_contrato', $contrato)
            ->firstOrFail();

        // Somente rascunhos podem ser salvos por etapa.
        // Campanhas rejeitadas usam o fluxo de update existente.
        if (!in_array($campanha->status, ['rascunho', 'Em elaboração'])) {
            return response()->json([
                'message' => 'Esta campanha não pode ser editada por etapa.',
            ], 422);
        }

        if (Auth::user()->perfis_id === 3) {
            return response()->json(['message' => 'Fiscais não podem editar campanhas.'], 403);
        }

        // Delega para o método correto conforme a etapa
        return match($etapa) {
            'apresentacao' => $this->salvarApresentacao($request, $campanha, $contrato),
            'metodologia'  => $this->salvarMetodologia($request, $campanha, $contrato),
            'resultados'   => $this->salvarResultados($request, $campanha, $contrato),
            'anexos'       => $this->salvarAnexos($request, $campanha, $contrato),
            default        => response()->json(['message' => 'Etapa inválida.'], 422),
        };
    }

    // -------------------------------------------------------------------------

    private function salvarApresentacao(Request $request, SgcFaunaCampanha $campanha, $contrato)
    {
        $validated = $request->validate(app(EtapaApresentacaoRequest::class)->rules());

        Log::info('SalvarEtapa DEBUG apresentacao', [
            'validated' => $validated,
            'request_all' => $request->all(),
        ]);

        try {
            DB::beginTransaction();

            $campanha->update([
                'etapa_atual'               => 'apresentacao',
                'id_campanha'               => $validated['id_campanha'] ?? $campanha->id_campanha,
                'cod_emp'                   => $validated['cod_emp'] ?? $campanha->cod_emp,
                'sei_dnit'                  => $validated['sei_dnit'] ?? $campanha->sei_dnit,
                'nao_se_aplica_quelo'       => $validated['nao_se_aplica_quelo'] ?? false,
                'nao_se_aplica_cavernicola' => $validated['nao_se_aplica_cavernicola'] ?? false,
            ]);

            if (!empty($validated['data_campanha_inicial']))
                $campanha->update(['data_ini' => $validated['data_campanha_inicial']]);
            if (!empty($validated['data_campanha_final']))
                $campanha->update(['data_fim' => $validated['data_campanha_final']]);
            if (!empty($validated['periodo']))
                $campanha->update(['periodo' => $validated['periodo']]);
            if (!empty($validated['observacoes']))
                $campanha->update(['observacoes' => $validated['observacoes']]);

            if (!empty($validated['id_abio'])) {
                SgcFaunaCampanhaAbios::where('campanha_id', $campanha->id)->delete();
                foreach ($validated['id_abio'] as $abioId) {
                    SgcFaunaCampanhaAbios::create([
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanha->id,
                        'n_abio'      => $abioId,
                    ]);
                }
            }

            $this->campanhaService->salvarSubDadosApresentacao(
                $contrato,
                $campanha->id,
                $validated
            );

            DB::commit();

            return response()->json([
                'message'     => 'Apresentação salva.',
                'etapa_atual' => $campanha->fresh()->etapa_atual,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalvarEtapa: erro em apresentacao', [
                'campanha_id' => $campanha->id,
                'erro'        => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    private function salvarMetodologia(Request $request, SgcFaunaCampanha $campanha, $contrato)
    {
        $validated = $request->validate(app(EtapaMetodologiaRequest::class)->rules());

        try {
            DB::beginTransaction();

            $this->campanhaService->salvarMetodologias(
                $contrato,
                $campanha->id,
                $validated['metodologias'] ?? []
            );

            $campanha->update([
                'etapa_atual' => $this->avancarEtapa($campanha->etapa_atual, 'metodologia'),
            ]);

            DB::commit();

            return response()->json([
                'message'     => 'Metodologia salva.',
                'etapa_atual' => $campanha->fresh()->etapa_atual,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalvarEtapa: erro em metodologia', [
                'campanha_id' => $campanha->id,
                'erro'        => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    private function salvarResultados(Request $request, SgcFaunaCampanha $campanha, $contrato)
    {
        $validated = $request->validate(app(EtapaResultadosRequest::class)->rules());

        try {
            DB::beginTransaction();

            if (!empty($validated['planilha_terrestre'])) {
                $this->campanhaService->salvarResultadosPorTipo(
                    $contrato,
                    $campanha->id,
                    $validated['planilha_terrestre'],
                    $validated['consideracoes'] ?? null,
                    'terrestre'
                );
            }

            if (!empty($validated['planilha_aquatica'])) {
                $this->campanhaService->salvarResultadosPorTipo(
                    $contrato,
                    $campanha->id,
                    $validated['planilha_aquatica'],
                    $validated['consideracoes'] ?? null,
                    'aquatica'
                );
            }

            if (!empty($validated['planilha_cavernicola'])) {
                $this->campanhaService->salvarResultadosPorTipo(
                    $contrato,
                    $campanha->id,
                    $validated['planilha_cavernicola'],
                    $validated['consideracoes'] ?? null,
                    'cavernicola'
                );
            }

            // Planilha e considerações de atropelamento
            if (!empty($validated['planilha_atropelamento'])) {
                $file = $request->file('planilha_atropelamento');
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    if ($campanha->planilha_atropelamento) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($campanha->planilha_atropelamento);
                    }
                    $campanha->update([
                        'planilha_atropelamento' => $file->storeAs('fauna_atropelamento', $filename, 'public'),
                    ]);
                }
            }
            if (isset($validated['consideracoes_atropelamento'])) {
                $campanha->update(['consideracoes_atropelamento' => $validated['consideracoes_atropelamento']]);
            }

            $campanha->update([
                'etapa_atual' => $this->avancarEtapa($campanha->etapa_atual, 'resultados'),
            ]);

            DB::commit();

            return response()->json([
                'message'     => 'Resultados salvos.',
                'etapa_atual' => $campanha->fresh()->etapa_atual,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalvarEtapa: erro em resultados', [
                'campanha_id' => $campanha->id,
                'erro'        => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    private function salvarAnexos(Request $request, SgcFaunaCampanha $campanha, $contrato)
    {
        $validated = $request->validate(app(EtapaAnexosRequest::class)->rules());

        try {
            DB::beginTransaction();

            $this->campanhaService->salvarAnexos(
                $contrato,
                $campanha->id,
                $request->file('anexos') ?? []
            );

            $campanha->update([
                'etapa_atual' => $this->avancarEtapa($campanha->etapa_atual, 'anexos'),
            ]);

            DB::commit();

            return response()->json([
                'message'     => 'Anexos salvos.',
                'etapa_atual' => $campanha->fresh()->etapa_atual,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SalvarEtapa: erro em anexos', [
                'campanha_id' => $campanha->id,
                'erro'        => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Erro ao salvar: ' . $e->getMessage()], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Avança etapa_atual apenas se a etapa sendo salva for igual ou posterior
    // à etapa atual — evita regredir o progresso ao reeditar uma aba anterior.
    // -------------------------------------------------------------------------
    private function avancarEtapa(?string $etapaAtual, string $etapaSalva): string
    {
        $ordemAtual = self::ORDEM_ETAPAS[$etapaAtual] ?? 0;
        $ordemSalva = self::ORDEM_ETAPAS[$etapaSalva] ?? 0;

        return $ordemSalva >= $ordemAtual ? $etapaSalva : $etapaAtual;
    }
}
