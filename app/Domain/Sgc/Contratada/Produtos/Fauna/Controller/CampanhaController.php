<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\CampanhaService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Services\FaunaFiscalService;
use App\Domain\Sgc\Contratada\Produtos\Services\ProdutosService;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\StoreCampanhaRequest;
use App\Domain\Sgc\Contratada\Produtos\Fauna\Requests\UpdateCampanhaRequest;
use App\Models\SgcFaunaCampanha;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcFaunaComentarios;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcFaunaAnaliseEtapa;
use App\Models\SgcFaunaModuloAmostral;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampanhaController extends Controller
{
    protected $campanhaService;
    protected $produtosService;
    protected $faunaFiscalService;

    public function __construct(CampanhaService $campanhaService, FaunaFiscalService $faunaFiscalService, ProdutosService $produtosService)
    {
        $this->campanhaService = $campanhaService;
        $this->produtosService = $produtosService;
        $this->faunaFiscalService = $faunaFiscalService;
    }

    public function salvarCampanha(StoreCampanhaRequest $request, $contrato, $produto)
    {
        $validated = $request->validated();
        $validated['anexos'] = $request->file('anexos') ?? [];
        $validated['planilha'] = $request->file('planilha');

        try {
            DB::beginTransaction();
            $campanhaId = $this->campanhaService->salvarCampanha($contrato, $validated);
            if (!empty($validated['id_abio'])) {
                SgcFaunaCampanhaAbios::where('campanha_id', $campanhaId)->delete();
                foreach ($validated['id_abio'] as $abioId) {
                    SgcFaunaCampanhaAbios::create([
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanhaId,
                        'n_abio' => $abioId,
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha salva com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CampanhaController: Erro ao salvar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'campanha_id' => $campanhaId ?? 'não fornecido',
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar campanha: ' . $e->getMessage()]);
        }
    }

    public function update(UpdateCampanhaRequest $request, $contrato, $produto, $campanhaId)
    {
        if (Auth::user()->perfis_id === 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem atualizar campanhas.']);
        }

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser atualizadas.']);
        }

        try {
            $validated = $request->validated();
            $validated['id_abio'] = array_map(function ($abio) {
                return isset($abio['abio']['id']) ? (int) $abio['abio']['id'] : null;
            }, (array) $request->input('abios', []));
            $validated['id_abio'] = array_filter($validated['id_abio'], fn($id) => !is_null($id));
            $validated['profissionais'] = array_map(function ($profissional) {
                return [
                    'id_profissional' => isset($profissional['profissional']['id']) ? (int) $profissional['profissional']['id'] : null,
                    'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                    'formacao' => isset($profissional['profissional']['formacao']) ? $profissional['profissional']['formacao'] : null,
                ];
            }, (array) $request->input('profissionais', []));
            $validated['anexos'] = $request->file('anexos') ?? [];
            $validated['planilha'] = $request->file('planilha');

            DB::beginTransaction();
            $campanhaId = $this->campanhaService->atualizarCampanha($contrato, $campanhaId, $validated);
            DB::commit();
            return redirect()->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CampanhaController: Erro ao atualizar campanha', [
                'contrato' => $contrato,
                'produto' => $produto,
                'campanha_id' => $campanhaId,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar campanha: ' . $e->getMessage()]);
        }
    }

    public function updatePartial(UpdateCampanhaRequest $request, $contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
        if ($campanha->status !== 'Em elaboração') {
            return redirect()->back()->withErrors(['error' => 'Apenas campanhas em elaboração podem ser atualizadas parciais.']);
        }
        $validated = $request->validate([
            'aba' => 'required|string|in:apresentacao,metodologia,resultados,anexos',
            'data' => 'required|array',
        ]);
        $this->campanhaService->atualizarParcialCampanha($campanha, $validated['aba'], $validated['data']);
        return redirect()->back()->with('success', 'Dados da aba salvos com sucesso!');
    }

    public function show($contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::with([
            'abios.abio',
            'profissionais.profissional',
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'resultados',
            'resultados_consideracoes',
            'anexos'
        ])->findOrFail($campanhaId);

        Log::debug('CampanhaController: Dados da campanha', [
            'campanha_id' => $campanhaId,
            'resultados_count' => $campanha->resultados->count(),
            'anexos_count' => $campanha->anexos->count(),
        ]);

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/VisualizarCampanha', [
            'campanha' => $this->mapCampanhaData($campanha),
            'contrato' => $campanha->id_contrato,
            'produto' => $campanha->subproduto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanha->status === 'Em análise',
        ]);
    }

    public function edit(Request $request, $contrato, $produto, $campanhaId)
    {
        $campanha = SgcFaunaCampanha::with([
            'abios' => fn($query) => $query->with(['abio' => fn($q) => $q->select('id', 'numero_licenca')]),
            'profissionais' => fn($query) => $query->with(['profissional' => fn($q) => $q->select('id', 'profissional', 'formacao')]),
            'modulos_amostrais',
            'pontos_quelo_crocod',
            'pontos_cavernicola',
            'metodologias',
            'analises',
            'anexos',
            'resultados',
            'resultados_consideracoes',
        ])->findOrFail($campanhaId);

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser editadas.']);
        }

        if (Auth::user()->perfis_id === 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem editar campanhas.']);
        }

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray();
        $abios = $this->produtosService->getAbios();
        $profissionais = $this->campanhaService->getProfissionaisByContrato($contrato);
        $ufs = [
            ['uf' => 'AC'], ['uf' => 'AL'], ['uf' => 'AP'], ['uf' => 'AM'], ['uf' => 'BA'],
            ['uf' => 'CE'], ['uf' => 'DF'], ['uf' => 'ES'], ['uf' => 'GO'], ['uf' => 'MA'],
            ['uf' => 'MT'], ['uf' => 'MS'], ['uf' => 'MG'], ['uf' => 'PA'], ['uf' => 'PB'],
            ['uf' => 'PR'], ['uf' => 'PE'], ['uf' => 'PI'], ['uf' => 'RJ'], ['uf' => 'RN'],
            ['uf' => 'RS'], ['uf' => 'RO'], ['uf' => 'RR'], ['uf' => 'SC'], ['uf' => 'SP'],
            ['uf' => 'SE'], ['uf' => 'TO']
        ];
        $biomas = ['Amazônia', 'Caatinga', 'Cerrado', 'Mata Atlântica', 'Pampa', 'Pantanal'];
        $comentarios = $this->campanhaService->getComentariosByCampanha($contrato, $campanhaId);

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/EditarCampanha', [
            'campanha' => array_merge($this->mapCampanhaData($campanha), [
                'resultados' => $campanha->resultados && $campanha->resultados->isNotEmpty() ? $campanha->resultados->map(function ($resultado) {
                    return [
                        'id' => $resultado->id,
                        'id_campanha' => $resultado->id_campanha,
                        'modulo' => $resultado->modulo,
                        'parcela' => $resultado->parcela,
                        'id_armadilha' => $resultado->id_armadilha,
                        'grupo_amostrado' => $resultado->grupo_amostrado,
                        'data_registro' => $resultado->data_registro,
                        'hora_registro' => $resultado->hora_registro,
                        'categoria' => $resultado->categoria,
                        'classe' => $resultado->classe,
                        'ordem' => $resultado->ordem,
                        'familia' => $resultado->familia,
                        'genero' => $resultado->genero,
                        'especie' => $resultado->especie,
                        'nome_comum' => $resultado->nome_comum,
                        'sexo' => $resultado->sexo,
                        'faixa_etaria' => $resultado->faixa_etaria,
                        'qnt_individuos' => $resultado->qnt_individuos,
                        'num_marcacao' => $resultado->num_marcacao,
                        'coletado' => $resultado->coletado,
                        'num_tombamento' => $resultado->num_tombamento,
                        'dados_biometricos' => $resultado->dados_biometricos,
                        'comp_total' => $resultado->comp_total,
                        'cabeca' => $resultado->cabeca,
                        'cauda' => $resultado->cauda,
                        'femur' => $resultado->femur,
                        'orelha' => $resultado->orelha,
                        'peso' => $resultado->peso,
                        'status_conservacao_federal' => $resultado->status_conservacao_federal,
                        'status_conservacao_iucn' => $resultado->status_conservacao_iucn,
                        'especies_bioindicadoras' => $resultado->especies_bioindicadoras ?? null,
                        'especies_alvo_monitoramento' => $resultado->especies_alvo_monitoramento ?? null,
                    ];
                })->toArray() : [],
                'resultados_consideracoes' => $campanha->resultados_consideracoes ? $campanha->resultados_consideracoes->consideracoes : null,
            ]),
            'contrato' => $contrato,
            'produto' => $produto,
            'abios' => $abios,
            'empreendimentos' => $empreendimentos,
            'subproduto' => $request->query('subproduto'),
            'profissionais' => $profissionais,
            'ufs' => $ufs,
            'biomas' => $biomas,
            'contratos' => [
                'tipo_contrato' => $campanha->tipo_contrato ?? 'N/A',
                'contratada' => $campanha->contratada ?? 'N/A',
            ],
            'comentarios' => $comentarios,
        ]);
    }

    public function analise($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 2) {
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
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
            return redirect()->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Campanha não está em análise.']);
        }

        $analises = $this->faunaFiscalService->getAnalisesByCampanha($contrato, $campanha);
        $comentarios = $this->campanhaService->getComentariosByCampanha($contrato, $campanha);

        return Inertia::render('Sgc/Contratada/Produtos/Fauna/AnaliseCampanha', [
            'campanha' => $this->mapCampanhaData($campanhaObj),
            'contrato' => $campanhaObj->id_contrato,
            'produto' => $campanhaObj->subproduto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 2 && $campanhaObj->status === 'Em análise',
            'analises' => $analises ?? [],
            'comentarios' => $comentarios ?? [],
        ]);
    }

    public function salvarAnalise(Request $request, $contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 2) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem salvar análises.']);
        }
        try {
            $validated = $request->validate([
                'etapa' => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
                'status' => 'required|string|in:Aprovada,Rejeitada',
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
            return redirect()->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                ->with('success', 'Análise da etapa salva com sucesso!');
        } catch (\Exception $e) {
            Log::error('CampanhaController: Erro ao salvar análise', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Erro ao salvar análise: ' . $e->getMessage()]);
        }
    }

    public function finalizarAvaliacao($contrato, $produto, $campanha)
    {
        try {
            $this->faunaFiscalService->finalizarAvaliacaoCampanha($contrato, $campanha);
            return Inertia::location(route('sgc.contratada.produtos.index', [$contrato, $produto]));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function mapCampanhaData($campanha)
    {
        $modulosManuais = SgcFaunaModuloAmostral::where('campanha_id', $campanha->id)->get();
        return [
            'id' => $campanha->id,
            'id_campanha' => $campanha->id_campanha,
            'cod_emp' => $campanha->cod_emp,
            'familia' => $campanha->subproduto,
            'data_campanha_inicial' => $campanha->data_ini,
            'data_campanha_final' => $campanha->data_fim,
            'periodo' => $campanha->periodo,
            'observacoes' => $campanha->observacoes,
            'nao_se_aplica' => $campanha->nao_se_aplica ?? false,
            'status' => $campanha->status,
            'formModuloAmostral' => $modulosManuais->isNotEmpty() ? $modulosManuais->map(function ($modulo) {
                return [
                    'id' => $modulo->id,
                    'data_cadastro' => $modulo->data_cadastro,
                    'tamanho_modulo' => $modulo->tamanho_modulo,
                    'uf' => $modulo->uf,
                    'municipio' => $modulo->municipio,
                    'bioma' => $modulo->bioma,
                    'fitofisionomia' => $modulo->fitofisionomia,
                    'latitude_inicial' => $modulo->latitude_inicial,
                    'longitude_inicial' => $modulo->longitude_inicial,
                    'latitude_final' => $modulo->latitude_final,
                    'longitude_final' => $modulo->longitude_final,
                    'arquivo' => $modulo->nome_arquivo,
                    'obs' => $modulo->obs,
                ];
            })->toArray() : [],
            'formPontosAmostragem' => $campanha->pontos_quelo_crocod && $campanha->pontos_quelo_crocod->isNotEmpty() ? $campanha->pontos_quelo_crocod->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'ponto_de_coleta' => $ponto->ponto_de_coleta,
                    'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'bacia' => $ponto->bacia_hidrografica,
                    'profundidade' => $ponto->profundidade,
                    'largura' => $ponto->largura,
                    'tipo_substrato' => $ponto->tipo_substrato,
                ];
            })->toArray() : [],
            'formPontosCavernicola' => $campanha->pontos_cavernicola && $campanha->pontos_cavernicola->isNotEmpty() ? $campanha->pontos_cavernicola->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'cavidade' => $ponto->cavidade,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                    'formacao_associada' => $ponto->formacao_associada,
                    'temperatura_media_interna' => $ponto->temperatura_media_interna,
                    'temperatura_media_externa' => $ponto->temperatura_media_externa,
                    'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                    'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
                ];
            })->toArray() : [],
            'formMetodologia' => $campanha->metodologias && $campanha->metodologias->isNotEmpty() ? $campanha->metodologias->map(function ($metodologia) {
                return [
                    'id' => $metodologia->id,
                    'grupo_faunistico' => $metodologia->grupo_faunistico,
                    'metodologia' => $metodologia->metodologia,
                ];
            })->toArray() : [],
            'formResultados' => $campanha->resultados && $campanha->resultados->isNotEmpty() ? [
                'id_campanha' => $campanha->resultados->last()->id_campanha,
                'modulo' => $campanha->resultados->last()->modulo,
                'parcela' => $campanha->resultados->last()->parcela,
                'id_armadilha' => $campanha->resultados->last()->id_armadilha,
                'grupo_amostrado' => $campanha->resultados->last()->grupo_amostrado,
                'data_registro' => $campanha->resultados->last()->data_registro,
                'hora_registro' => $campanha->resultados->last()->hora_registro,
                'categoria' => $campanha->resultados->last()->categoria,
                'classe' => $campanha->resultados->last()->classe,
                'ordem' => $campanha->resultados->last()->ordem,
                'familia' => $campanha->resultados->last()->familia,
                'genero' => $campanha->resultados->last()->genero,
                'especie' => $campanha->resultados->last()->especie,
                'nome_comum' => $campanha->resultados->last()->nome_comum,
                'sexo' => $campanha->resultados->last()->sexo,
                'faixa_etaria' => $campanha->resultados->last()->faixa_etaria,
                'qnt_individuos' => $campanha->resultados->last()->qnt_individuos,
                'num_marcacao' => $campanha->resultados->last()->num_marcacao,
                'coletado' => $campanha->resultados->last()->coletado,
                'num_tombamento' => $campanha->resultados->last()->num_tombamento,
                'dados_biometricos' => $campanha->resultados->last()->dados_biometricos,
                'comp_total' => $campanha->resultados->last()->comp_total,
                'cabeca' => $campanha->resultados->last()->cabeca,
                'cauda' => $campanha->resultados->last()->cauda,
                'femur' => $campanha->resultados->last()->femur,
                'orelha' => $campanha->resultados->last()->orelha,
                'peso' => $campanha->resultados->last()->peso,
                'status_conservacao_federal' => $campanha->resultados->last()->status_conservacao_federal,
                'status_conservacao_iucn' => $campanha->resultados->last()->status_conservacao_iucn,
                'especies_bioindicadoras' => $campanha->resultados->last()->especies_bioindicadoras ?? null,
                'especies_alvo_monitoramento' => $campanha->resultados->last()->especies_alvo_monitoramento ?? null,
            ] : [
                'id_campanha' => null,
                'modulo' => null,
                'parcela' => null,
                'id_armadilha' => null,
                'grupo_amostrado' => null,
                'data_registro' => null,
                'hora_registro' => null,
                'categoria' => null,
                'classe' => null,
                'ordem' => null,
                'familia' => null,
                'genero' => null,
                'especie' => null,
                'nome_comum' => null,
                'sexo' => null,
                'faixa_etaria' => null,
                'qnt_individuos' => null,
                'num_marcacao' => null,
                'coletado' => null,
                'num_tombamento' => null,
                'dados_biometricos' => null,
                'comp_total' => null,
                'cabeca' => null,
                'cauda' => null,
                'femur' => null,
                'orelha' => null,
                'peso' => null,
                'status_conservacao_federal' => null,
                'status_conservacao_iucn' => null,
                'especies_bioindicadoras' => null,
                'especies_alvo_monitoramento' => null,
            ],
            'consideracoes' => $campanha->resultados_consideracoes ? $campanha->resultados_consideracoes->consideracoes : null,
            'abios' => $campanha->abios && $campanha->abios->isNotEmpty() ? $campanha->abios->map(function ($abio) {
                return [
                    'id' => $abio->n_abio,
                    'abio' => ['numero_licenca' => $abio->abio->numero_licenca ?? 'N/A'],
                ];
            })->toArray() : [],
            'profissionais' => $campanha->profissionais && $campanha->profissionais->isNotEmpty() ? $campanha->profissionais->map(function ($prof) {
                return [
                    'id' => $prof->id,
                    'profissional' => $prof->profissional->profissional ?? 'N/A',
                    'grupo_faunistico' => $prof->grupo_faunistico,
                    'formacao' => $prof->profissional->formacao ?? 'N/A',
                    'funcao' => $prof->profissional->funcao ?? 'N/A',
                    'ctf' => $prof->profissional->ctf ?? 'N/A',
                ];
            })->toArray() : [],
            'modulos_amostrais' => $campanha->modulos_amostrais && $campanha->modulos_amostrais->isNotEmpty() ? $campanha->modulos_amostrais->map(function ($modulo) {
                return [
                    'id' => $modulo->id,
                    'data_cadastro' => $modulo->data_cadastro,
                    'tamanho_modulo' => $modulo->tamanho_modulo,
                    'uf' => $modulo->uf,
                    'municipio' => $modulo->municipio,
                    'bioma' => $modulo->bioma,
                    'fitofisionomia' => $modulo->fitofisionomia,
                    'latitude_inicial' => $modulo->latitude_inicial,
                    'longitude_inicial' => $modulo->longitude_inicial,
                    'latitude_final' => $modulo->latitude_final,
                    'longitude_final' => $modulo->longitude_final,
                    'obs' => $modulo->obs,
                    'arquivo' => $modulo->nome_arquivo,
                ];
            })->toArray() : [],
            'pontos_quelo_crocod' => $campanha->pontos_quelo_crocod && $campanha->pontos_quelo_crocod->isNotEmpty() ? $campanha->pontos_quelo_crocod->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'ponto_de_coleta' => $ponto->ponto_de_coleta,
                    'nome_curso_hidrico' => $ponto->nome_curso_hidrico,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'bacia' => $ponto->bacia_hidrografica,
                    'profundidade' => $ponto->profundidade,
                    'largura' => $ponto->largura,
                    'tipo_substrato' => $ponto->tipo_substrato,
                ];
            })->toArray() : [],
            'pontos_cavernicola' => $campanha->pontos_cavernicola && $campanha->pontos_cavernicola->isNotEmpty() ? $campanha->pontos_cavernicola->map(function ($ponto) {
                return [
                    'id' => $ponto->id,
                    'cavidade' => $ponto->cavidade,
                    'latitude' => $ponto->latitude,
                    'longitude' => $ponto->longitude,
                    'distancia_eixo_rodovia' => $ponto->distancia_eixo_rodovia,
                    'formacao_associada' => $ponto->formacao_associada,
                    'temperatura_media_interna' => $ponto->temperatura_media_interna,
                    'temperatura_media_externa' => $ponto->temperatura_media_externa,
                    'umidade_relativa_interna' => $ponto->umidade_relativa_interna,
                    'umidade_relativa_externa' => $ponto->umidade_relativa_externa,
                ];
            })->toArray() : [],
            'metodologias' => $campanha->metodologias && $campanha->metodologias->isNotEmpty() ? $campanha->metodologias->map(function ($metodologia) {
                return [
                    'id' => $metodologia->id,
                    'grupo_faunistico' => $metodologia->grupo_faunistico,
                    'metodologia' => $metodologia->metodologia,
                ];
            })->toArray() : [],
            'resultados' => $campanha->resultados && $campanha->resultados->isNotEmpty() ? $campanha->resultados->map(function ($resultado) {
                return [
                    'id' => $resultado->id,
                    'modulo' => $resultado->modulo,
                    'parcela' => $resultado->parcela,
                    'id_armadilha' => $resultado->id_armadilha,
                    'grupo_amostrado' => $resultado->grupo_amostrado,
                    'data_registro' => $resultado->data_registro,
                    'hora_registro' => $resultado->hora_registro,
                    'categoria' => $resultado->categoria,
                    'classe' => $resultado->classe,
                    'ordem' => $resultado->ordem,
                    'familia' => $resultado->familia,
                    'genero' => $resultado->genero,
                    'especie' => $resultado->especie,
                    'nome_comum' => $resultado->nome_comum,
                    'sexo' => $resultado->sexo,
                    'faixa_etaria' => $resultado->faixa_etaria,
                    'qnt_individuos' => $resultado->qnt_individuos,
                    'num_marcacao' => $resultado->num_marcacao,
                    'coletado' => $resultado->coletado,
                    'num_tombamento' => $resultado->num_tombamento,
                    'dados_biometricos' => $resultado->dados_biometricos,
                    'comp_total' => $resultado->comp_total,
                    'cabeca' => $resultado->cabeca,
                    'cauda' => $resultado->cauda,
                    'femur' => $resultado->femur,
                    'orelha' => $resultado->orelha,
                    'peso' => $resultado->peso,
                    'status_conservacao_federal' => $resultado->status_conservacao_federal,
                    'status_conservacao_iucn' => $resultado->status_conservacao_iucn,
                    'especies_bioindicadoras' => $resultado->especies_bioindicadoras ?? null,
                    'especies_alvo_monitoramento' => $resultado->especies_alvo_monitoramento ?? null,
                ];
            })->toArray() : [],
            'anexos' => $campanha->anexos && $campanha->anexos->isNotEmpty() ? $campanha->anexos->map(function ($anexo) {
                return [
                    'id' => $anexo->id,
                    'tipo_anexo' => $anexo->tipo_anexo,
                    'caminho' => Storage::url($anexo->caminho),
                    'nome_arquivo' => $anexo->nome_arquivo ?? basename($anexo->caminho),
                    'created_at' => $anexo->created_at,
                ];
            })->toArray() : [],
            'analises' => $campanha->analises && $campanha->analises->isNotEmpty() ? $campanha->analises->map(function ($analise) {
                return [
                    'id' => $analise->id,
                    'analise' => $analise->analise,
                    'etapa' => $analise->etapa,
                    'status' => $analise->status,
                    'observacoes' => $analise->observacoes,
                    'created_at' => $analise->created_at,
                ];
            })->toArray() : [],
        ];
    }
}