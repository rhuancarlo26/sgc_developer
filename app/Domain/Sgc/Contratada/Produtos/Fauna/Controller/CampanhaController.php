<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Resources\CampanhaResource;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\CampanhaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaFiscalService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\ModeloService;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\UpdateCampanhaRequest;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaCampanhaProfissional;
use App\Models\SgcMalarigeno;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CampanhaController extends Controller
{
    public function __construct(
        protected CampanhaService $campanhaService,
        protected FaunaFiscalService $faunaFiscalService,
        protected ProdutosService $produtosService,
        protected ModeloService $modeloService,
    ) {}

    public function show($contrato, $produto, $campanhaId, $modulo = null)
    {
        if ($produto === 'malarigeno') {
            $campanha = SgcMalarigeno::with(['modulo', 'fotos', 'anexos'])
                ->where('id_contrato', $contrato)
                ->findOrFail($campanhaId);

            $campanhaData = [
                'id' => $campanha->id,
                'id_contrato' => $campanha->id_contrato,
                'subproduto' => $campanha->subproduto,
                'modulo_id' => $campanha->modulo_id,
                'modulo' => $campanha->modulo?->only(['id', 'nome', 'nome_planilha_modelo']),
                'status' => $campanha->status,
                'planilha_nome' => $campanha->planilha_nome,
                'planilha_caminho' => $campanha->planilha_caminho,
                'planilha_url' => $campanha->planilha_caminho ? Storage::url($campanha->planilha_caminho) : null,
                'created_at' => optional($campanha->created_at)->format('d/m/Y H:i'),
                'updated_at' => optional($campanha->updated_at)->format('d/m/Y H:i'),
                'fotos' => $campanha->fotos->map(fn($foto) => [
                    'id' => $foto->id,
                    'nome_arquivo' => $foto->nome_arquivo,
                    'caminho_arquivo' => $foto->caminho_arquivo,
                    'url' => $foto->caminho_arquivo ? Storage::url($foto->caminho_arquivo) : null,
                    'latitude' => $foto->latitude,
                    'longitude' => $foto->longitude,
                    'data_captura' => optional($foto->data_captura)->format('d/m/Y H:i'),
                    'descricao' => $foto->descricao,
                ])->values(),
                'anexos' => $campanha->anexos->map(fn($anexo) => [
                    'id' => $anexo->id,
                    'nome_arquivo' => $anexo->nome_arquivo,
                    'caminho_arquivo' => $anexo->caminho_arquivo,
                    'url' => $anexo->caminho_arquivo ? Storage::url($anexo->caminho_arquivo) : null,
                ])->values(),
            ];

            return Inertia::render('Sgc/Contratada/Produtos/Malarigeno/VisualizarCampanha', [
                'campanha' => $campanhaData,
                'campanha_id' => $campanhaId,
                'contrato' => $campanha->id_contrato,
                'produto' => 'malarigeno',
                'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
                'canApprove' => Auth::user()->perfis_id === 3 && $campanha->status === 'Em análise',
            ]);
        }

        $moduloativo = "Fauna";
        $campanhaData = null;
        
        // ✅ Se for Espeleologia, carrega o modelo específico
        if ($modulo === 'espeleologia') {
            Log::debug('CampanhaController: Módulo de Espeleologia acessado', [
                'campanha_id' => $campanhaId,
            ]);
            
            $campanha = SgcEspeleoCampanha::findOrFail($campanhaId);
            $moduloativo = "Espeleologia";
            
            // ✅ Para Espeleologia, converte o modelo diretamente para array
            // sem usar CampanhaResource (que é específico de Fauna)
            $campanhaData = $campanha->toArray();
            
        } else {
            // ✅ Senão, carrega Fauna normalmente (compatível com produção)
            $campanha = SgcFaunaCampanha::with([
                'abios.abio',
                'profissionais.profissional',
                'modulos_amostrais',
                'pontos_quelo_crocod',
                'pontos_cavernicola',
                'metodologias',
                'resultadosTerrestre',
                'resultadosAquatica',
                'resultadosCavernicola',
                'resultados_consideracoes',
                'anexos',
                'atropelamento_campanhas',
            ])->findOrFail($campanhaId);

            Log::debug('FaunaController: resultados por grupo', [
                'campanha_id'          => $campanhaId,
                'terrestre_count'      => $campanha->resultadosTerrestre->count(),
                'aquatica_count'       => $campanha->resultadosAquatica->count(),
                'cavernicola_count'    => $campanha->resultadosCavernicola->count(),
            ]);
            
            // ✅ Para Fauna, usa CampanhaResource (como em produção)
            $campanhaData = CampanhaResource::toArray($campanha);
        }
        
        // ✅ Busca empreendimento (funciona para ambos os módulos)
        $empreendimento = SgcvwEmpreendimentos::where('cod_emp', $campanha->cod_emp)->first();
        $coordenadas = $empreendimento->coordenadas ?? null;

        // ✅ Renderiza a view correta baseado no módulo
        return Inertia::render('Sgc/Contratada/Produtos/' . $moduloativo . '/VisualizarCampanha', [
            'campanha'   => $campanhaData,
            'campanha_id' => $campanhaId,
            'contrato'   => $campanha->id_contrato,
            'produto'    => $campanha->subproduto,
            'contratos'  => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanha->status === 'Em análise',
            'coordenadas' => $coordenadas,
        ]);
    }


    // -------------------------------------------------------------------------
    // EDITAR (formulário) — apenas campanhas Rejeitadas ou Em elaboração
    // -------------------------------------------------------------------------

    public function edit(Request $request, $contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::with([
            'abios'                => fn($q) => $q->with(['abio' => fn($q) => $q->select('id', 'numero_licenca')]),
            'profissionais'        => fn($q) => $q->with(['profissional' => fn($q) => $q->select('id', 'profissional', 'formacao')]),
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'analises' => fn($q) => $q->with(['fiscal' => fn($q) => $q->select('id', 'name')]),  // ← ADICIONA RELAÇÃO COM FISCAL
            'anexos',
            'resultados',
            'resultados_consideracoes',
        ])->findOrFail($campanhaId);

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser editadas.']);
        }

        if (Auth::user()->perfis_id === 3) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem editar campanhas.']);
        }

        $comentarios = $this->campanhaService->getComentariosByCampanha($contrato, $campanhaId);

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/EditarCampanha', [
            'campanha'        => CampanhaResource::toArray($campanha),
            'contrato'        => $contrato,
            'produto'         => $produto,
            'abios'           => $this->produtosService->getAbios(),
            'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(),
            'subproduto'      => $request->query('subproduto'),
            'profissionais'   => $this->campanhaService->getProfissionaisByContrato($contrato),
            'ufs'             => self::getUfs(),
            'biomas'          => self::getBiomas(),
            'contratos'       => [
                'tipo_contrato' => $campanha->tipo_contrato ?? 'N/A',
                'contratada'    => $campanha->contratada ?? 'N/A',
            ],
            'comentarios'     => $comentarios,
        ]);
    }

    // -------------------------------------------------------------------------
    // ATUALIZAR — fluxo de reenvio após rejeição
    // -------------------------------------------------------------------------

    public function update(UpdateCampanhaRequest $request, $contrato, $produto, $campanhaId)
    {
        if (Auth::user()->perfis_id === 2) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem atualizar campanhas.']);
        }

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser atualizadas.']);
        }

        try {
            $validated = $request->validated();

            $validated['id_abio'] = array_filter(
                array_map(
                    fn($abio) => isset($abio['abio']['id']) ? (int) $abio['abio']['id'] : null,
                    (array) $request->input('abios', [])
                ),
                fn($id) => !is_null($id)
            );

            $validated['profissionais'] = array_map(fn($p) => [
                'id_profissional'  => isset($p['profissional']['id']) ? (int) $p['profissional']['id'] : null,
                'grupo_faunistico' => $p['grupo_faunistico'] ?? null,
                'formacao'         => $p['profissional']['formacao'] ?? null,
            ], (array) $request->input('profissionais', []));

            $validated['anexos']                       = $request->file('anexos') ?? [];
            $validated['planilha_terrestre']           = $request->file('planilha_terrestre');
            $validated['planilha_aquatica']            = $request->file('planilha_aquatica');
            $validated['planilha_cavernicola']         = $request->file('planilha_cavernicola');
            $validated['atropelamento_campanha']       = $request->input('atropelamento_campanha', []);
            $validated['planilha_atropelamento']       = $request->file('planilha_atropelamento');
            $validated['consideracoes_atropelamento']  = $request->input('consideracoes_atropelamento');

            DB::beginTransaction();
            $this->campanhaService->atualizarCampanha($contrato, $campanhaId, $validated);
            DB::commit();

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CampanhaController@update: erro', [
                'contrato'    => $contrato,
                'campanha_id' => $campanhaId,
                'erro'        => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar campanha: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // ANÁLISE — apenas fiscais (perfis_id === 3)
    // -------------------------------------------------------------------------

    public function analise($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Acesso negado. Apenas fiscais podem analisar campanhas.']);
        }

        $campanhaObj = SgcFaunaCampanha::with([
            'abios.abio',
            'profissionais.profissional',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'resultados',
            'resultados_consideracoes',
            'anexos',
        ])->findOrFail($campanha);

        if ($campanhaObj->status !== 'Em análise') {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Campanha não está em análise.']);
        }

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/AnaliseCampanha', [
            'campanha'    => CampanhaResource::toArray($campanhaObj),
            'contrato'    => $campanhaObj->id_contrato,
            'produto'     => $campanhaObj->subproduto,
            'contratos'   => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove'  => Auth::user()->perfis_id === 3 && $campanhaObj->status === 'Em análise',
            'analises'    => $this->faunaFiscalService->getAnalisesByCampanha($contrato, $campanha) ?? [],
            'comentarios' => $this->campanhaService->getComentariosByCampanha($contrato, $campanha) ?? [],
        ]);
    }

    // -------------------------------------------------------------------------
    // SALVAR ANÁLISE — fiscal avalia cada etapa
    // -------------------------------------------------------------------------

    public function salvarAnalise(Request $request, $contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem salvar análises.']);
        }

        try {
            $validated = $request->validate([
                'etapa'       => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
                'status'      => 'required|string|in:Aprovada,Rejeitada',
                'observacoes' => 'nullable|string',
            ]);

            $hasPreviousRejection = SgcFaunaAnaliseEtapa::where('id_contrato', $contrato)
                ->where('id_campanha', $campanha)
                ->where('etapa', $validated['etapa'])
                ->where('fiscal_id', Auth::id())
                ->where('status', 'Rejeitada')
                ->exists();

            if ($hasPreviousRejection) {
                $validated['nova_analise'] = true;
            }

            $this->faunaFiscalService->salvarAnaliseEtapa($contrato, $campanha, $validated);

            return redirect()
                ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                ->with('success', 'Análise da etapa salva com sucesso!');

        } catch (\Exception $e) {
            Log::error('CampanhaController@salvarAnalise: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro'        => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao salvar análise: ' . $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // FINALIZAR AVALIAÇÃO — fiscal conclui a análise
    // -------------------------------------------------------------------------

    public function finalizarAvaliacao($contrato, $produto, $campanha)
    {
        try {
            $this->faunaFiscalService->finalizarAvaliacaoCampanha($contrato, $campanha);

            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, 'fauna']));

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // -------------------------------------------------------------------------
    // Download dos modelos de planilhas
    // 
    public function downloadModelos()
    {
        $zipPath = $this->modeloService->gerarZipModelos();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


    public function deleteProfissional(
        $contrato,
        $produto,
        $campaignId,
        $profissionalVinculoId
    ) {
        // ────────────────────────────────────────────────────────────────
        // VALIDAÇÕES
        // ────────────────────────────────────────────────────────────────
        
        if (Auth::user()->perfis_id === 3) {
            return response()->json([
                'message' => 'Acesso negado. Fiscais não podem deletar profissionais.',
                'success' => false,
            ], 403);
        }

        try {
            // 1. Validar que a CAMPANHA existe e pertence ao CONTRATO
            $campanha = SgcFaunaCampanha::where('id', $campaignId)
                ->where('id_contrato', $contrato)
                ->firstOrFail();

            Log::info('CampanhaController@deleteProfissional - Campanha encontrada:', [
                'campaign_id' => $campaignId,
                'contrato_id' => $contrato,
                'campanha_status' => $campanha->status,
            ]);

            // 2. Validar que o VÍNCULO existe e pertence à CAMPANHA
            $profissionalVinculo = SgcFaunaCampanhaProfissional::where('id', $profissionalVinculoId)
                ->where('campanha_id', $campaignId)
                ->firstOrFail();

            Log::info('CampanhaController@deleteProfissional - Vínculo encontrado:', [
                'profissional_vinculo_id' => $profissionalVinculoId,
                'campaign_id' => $campaignId,
                'profissional_id' => $profissionalVinculo->profissional_id,
                'usuario_id' => Auth::id(),
            ]);

            // ────────────────────────────────────────────────────────────────
            // DELETAR O REGISTRO DE VINCULAÇÃO
            // ────────────────────────────────────────────────────────────────
            $profissionalVinculo->delete();

            Log::info('CampanhaController@deleteProfissional - Sucesso:', [
                'profissional_vinculo_id' => $profissionalVinculoId,
                'campaign_id' => $campaignId,
                'status' => 'Vínculo deletado com sucesso',
            ]);

            return response()->json([
                'message' => 'Profissional removido da campanha com sucesso!',
                'success' => true,
                'profissional_vinculo_id' => $profissionalVinculoId,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('CampanhaController@deleteProfissional - Não encontrado:', [
                'profissional_vinculo_id' => $profissionalVinculoId,
                'campaign_id' => $campaignId,
                'contrato_id' => $contrato,
            ]);

            return response()->json([
                'message' => 'Profissional ou campanha não encontrado.',
                'success' => false,
            ], 404);

        } catch (\Exception $e) {
            Log::error('CampanhaController@deleteProfissional - ERRO:', [
                'profissional_vinculo_id' => $profissionalVinculoId,
                'campaign_id' => $campaignId,
                'contrato_id' => $contrato,
                'produto' => $produto,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erro ao deletar profissional: ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }

    public function arquivar($contrato, $produto, $campanha)
    {
        $campanhaObj = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('id', $campanha)
            ->firstOrFail();

        $campanhaObj->update([
            'arquivada_em' => now(),
        ]);

        return redirect()
            ->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', 'Campanha arquivada com sucesso.');
    }

    public function restaurar($contrato, $produto, $campanha)
    {
        $campanhaObj = SgcFaunaCampanha::where('id_contrato', $contrato)
            ->where('id', $campanha)
            ->firstOrFail();

        $campanhaObj->update([
            'arquivada_em' => null,
        ]);

        return redirect()
            ->route('sgc.contratada.produtos.index', [$contrato, $produto])
            ->with('success', 'Campanha restaurada com sucesso.');
    }


    // -------------------------------------------------------------------------
    // Dados estáticos centralizados — usados aqui e no ProdutosController
    // -------------------------------------------------------------------------

    public static function getUfs(): array
    {
        return [
            'AC','AL','AP','AM','BA','CE','DF','ES','GO',
            'MA','MT','MS','MG','PA','PB','PR','PE','PI',
            'RJ','RN','RS','RO','RR','SC','SP','SE','TO',
        ];
    }

    public static function getBiomas(): array
    {
        return ['Amazônia', 'Caatinga', 'Cerrado', 'Mata Atlântica', 'Pampa', 'Pantanal'];
    }

    // ─────────────────────────────────────────────────────────────────────
    // DOWNLOAD — planilha modelo de resultados de atropelamento
    // ─────────────────────────────────────────────────────────────────────
    public function downloadModeloAtropelamento(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resultados Atropelamento');

        $headers = [
            'Data do Registro', 'Rodovia', 'KM', 'Latitude', 'Longitude',
            'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie',
            'Nome Científico', 'Nome Comum', 'Sexo', 'Faixa Etária',
            'Condição do Animal', 'Status Conservação IUCN', 'Status Conservação MMA', 'Observações',
        ];

        foreach ($headers as $col => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1) . '1';
            $sheet->setCellValue($cell, $header);
        }

        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers))) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'modelo_atropelamento_fauna.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // APROVAR TODAS AS ETAPAS DE UMA VEZ
    // ─────────────────────────────────────────────────────────────────────
    public function aprovarTudo($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem aprovar.']);
        }

        try {
            $campanhaObj = SgcFaunaCampanha::findOrFail($campanha);

            if ($campanhaObj->status !== 'Em análise') {
                return redirect()
                    ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                    ->withErrors(['error' => 'Campanha não está em análise.']);
            }

            // Etapas a serem aprovadas
            $etapas = [
                'apresentacao_geral',
                'caracterizacao_area',
                'modulos_amostrais',
                'pontos_quelo_crocod',
                'pontos_cavernicola',
                'metodologia',
                'resultados',
                'anexos',
            ];

            // Aprovar todas as etapas
            foreach ($etapas as $etapa) {
                $this->faunaFiscalService->salvarAnaliseEtapa($contrato, $campanha, [
                    'etapa' => $etapa,
                    'status' => 'Aprovada',
                    'observacoes' => null,
                ]);
            }

            // Mudar status da campanha para Aprovada
            $campanhaObj->update(['status' => 'Aprovada']);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha aprovada com sucesso!');

        } catch (\Exception $e) {
            Log::error('CampanhaController@aprovarTudo: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao aprovar campanha: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // REPROVAR TODAS AS ETAPAS DE UMA VEZ (COM JUSTIFICATIVA COMUM)
    // ─────────────────────────────────────────────────────────────────────
    public function reprovarTudo(Request $request, $contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem reprovar.']);
        }

        try {
            $validated = $request->validate([
                'comentario' => 'required|string|min:10',
            ], [
                'comentario.required' => 'A justificativa é obrigatória.',
                'comentario.min' => 'A justificativa deve ter no mínimo 10 caracteres.',
            ]);

            $campanhaObj = SgcFaunaCampanha::findOrFail($campanha);

            if ($campanhaObj->status !== 'Em análise') {
                return redirect()
                    ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                    ->withErrors(['error' => 'Campanha não está em análise.']);
            }

            // Etapas a serem reprovadas
            $etapas = [
                'apresentacao_geral',
                'caracterizacao_area',
                'modulos_amostrais',
                'pontos_quelo_crocod',
                'pontos_cavernicola',
                'metodologia',
                'resultados',
                'anexos',
            ];

            // Reprovar todas as etapas com o MESMO comentário
            foreach ($etapas as $etapa) {
                $this->faunaFiscalService->salvarAnaliseEtapa($contrato, $campanha, [
                    'etapa' => $etapa,
                    'status' => 'Rejeitada',
                    'observacoes' => $validated['comentario'],
                ]);
            }

            // Mudar status da campanha para Rejeitada
            $campanhaObj->update(['status' => 'Rejeitada']);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha rejeitada com sucesso!');

        } catch (\Exception $e) {
            Log::error('CampanhaController@reprovarTudo: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao reprovar campanha: ' . $e->getMessage()]);
        }
    }

}
