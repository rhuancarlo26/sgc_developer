<?php

namespace App\Domain\Sgc\Contratada\Produtos\Malarigeno\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Malarigeno\Services\MalarigenoFiscalService;
use App\Domain\Sgc\Contratada\Produtos\Malarigeno\Requests\MalarigenoAnaliseRequest;
use App\Models\SgcMalarigeno;
use App\Models\SgcModulo;
use App\Models\SgcvwEmpreendimentos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class MalarigenoCampanhaController extends Controller
{
    public function __construct(
        protected MalarigenoFiscalService $malarigenoFiscalService,
    ) {}

    public function show($contrato, $produto, $campanhaId)
    {
        $campanha = SgcMalarigeno::with(['modulo', 'fotos', 'anexos', 'analises.fiscal'])
            ->where('id_contrato', $contrato)
            ->findOrFail($campanhaId);

        $campanhaData = [
            'id' => $campanha->id,
            'id_campanha' => $campanha->id_campanha,
            'cod_emp' => $campanha->cod_emp,
            'sei_dnit' => $campanha->sei_dnit,
            'id_contrato' => $campanha->id_contrato,
            'subproduto' => $campanha->subproduto,
            'modulo_id' => $campanha->modulo_id,
            'modulo' => $campanha->modulo?->only(['id', 'nome', 'nome_planilha_modelo']),
            'status' => $campanha->status,
            'versao_analise' => $campanha->versao_analise,
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
            'analises' => $campanha->analises->map(fn($analise) => [
                'id' => $analise->id,
                'versao' => $analise->versao_analise,
                'status' => $analise->status,
                'observacoes' => $analise->observacoes,
                'fiscal' => $analise->fiscal ? [
                    'id' => $analise->fiscal->id,
                    'name' => $analise->fiscal->name,
                ] : null,
                'created_at' => optional($analise->created_at)->format('d/m/Y H:i'),
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

    public function analise($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Acesso negado. Apenas fiscais podem analisar campanhas.']);
        }

        $campanhaObj = SgcMalarigeno::with(['modulo', 'fotos', 'anexos'])
            ->findOrFail($campanha);

        if ($campanhaObj->status !== 'Em análise') {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanha])
                ->withErrors(['error' => 'Campanha não está em análise.']);
        }

        $campanhaData = [
            'id' => $campanhaObj->id,
            'id_campanha' => $campanhaObj->id_campanha,
            'cod_emp' => $campanhaObj->cod_emp,
            'sei_dnit' => $campanhaObj->sei_dnit,
            'id_contrato' => $campanhaObj->id_contrato,
            'subproduto' => $campanhaObj->subproduto,
            'modulo_id' => $campanhaObj->modulo_id,
            'modulo' => $campanhaObj->modulo?->only(['id', 'nome', 'nome_planilha_modelo']),
            'status' => $campanhaObj->status,
            'versao_analise' => $campanhaObj->versao_analise,
            'planilha_nome' => $campanhaObj->planilha_nome,
            'planilha_url' => $campanhaObj->planilha_caminho ? Storage::url($campanhaObj->planilha_caminho) : null,
            'created_at' => optional($campanhaObj->created_at)->format('d/m/Y H:i'),
            'fotos' => $campanhaObj->fotos->map(fn($foto) => [
                'id' => $foto->id,
                'url' => $foto->caminho_arquivo ? Storage::url($foto->caminho_arquivo) : null,
                'latitude' => $foto->latitude,
                'longitude' => $foto->longitude,
                'descricao' => $foto->descricao,
            ])->values(),
            'anexos' => $campanhaObj->anexos->map(fn($anexo) => [
                'id' => $anexo->id,
                'nome_arquivo' => $anexo->nome_arquivo,
                'url' => $anexo->caminho_arquivo ? Storage::url($anexo->caminho_arquivo) : null,
            ])->values(),
        ];

        return Inertia::render('Sgc/Contratada/Produtos/Malarigeno/AnaliseCampanha', [
            'campanha' => $campanhaData,
            'contrato' => $campanhaObj->id_contrato,
            'produto' => $campanhaObj->subproduto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
            'canApprove' => Auth::user()->perfis_id === 3 && $campanhaObj->status === 'Em análise',
            'analises' => $this->malarigenoFiscalService->getAnalisesByCampanha($contrato, $campanha) ?? [],
        ]);
    }

    public function salvarAnalise(MalarigenoAnaliseRequest $request, $contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem salvar análises.']);
        }

        try {
            $validated = $request->validated();

            $this->malarigenoFiscalService->salvarAnalise($contrato, $campanha, $validated);

            return redirect()
                ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                ->with('success', 'Análise salva com sucesso!');

        } catch (\Exception $e) {
            Log::error('MalarigenoCampanhaController@salvarAnalise: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao salvar análise: ' . $e->getMessage()]);
        }
    }

    public function finalizarAvaliacao($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem finalizar avaliações.']);
        }

        try {
            $this->malarigenoFiscalService->finalizarAvaliacaoCampanha($contrato, $campanha);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, 'malarigeno'])
                ->with('success', 'Campanha avaliada com sucesso!');

        } catch (\Exception $e) {
            Log::error('MalarigenoCampanhaController@finalizarAvaliacao: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao finalizar avaliação: ' . $e->getMessage()]);
        }
    }

    public function aprovarTudo($contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem aprovar.']);
        }

        try {
            $campanhaObj = SgcMalarigeno::findOrFail($campanha);

            if ($campanhaObj->status !== 'Em análise') {
                return redirect()
                    ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                    ->withErrors(['error' => 'Campanha não está em análise.']);
            }

            $this->malarigenoFiscalService->salvarAnalise($contrato, $campanha, [
                'status' => 'Aprovada',
                'observacoes' => null,
            ]);

            $this->malarigenoFiscalService->finalizarAvaliacaoCampanha($contrato, $campanha);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha aprovada com sucesso!');

        } catch (\Exception $e) {
            Log::error('MalarigenoCampanhaController@aprovarTudo: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao aprovar campanha: ' . $e->getMessage()]);
        }
    }

    public function reprovarTudo(Request $request, $contrato, $produto, $campanha)
    {
        if (Auth::user()->perfis_id !== 3) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado. Apenas fiscais podem reprovar.']);
        }

        try {
            $validated = $request->validate([
                'observacoes' => 'required|string|min:10',
            ], [
                'observacoes.required' => 'A justificativa é obrigatória.',
                'observacoes.min' => 'A justificativa deve ter no mínimo 10 caracteres.',
            ]);

            $campanhaObj = SgcMalarigeno::findOrFail($campanha);

            if ($campanhaObj->status !== 'Em análise') {
                return redirect()
                    ->route('sgc.contratada.produtos.analise', [$contrato, $produto, $campanha])
                    ->withErrors(['error' => 'Campanha não está em análise.']);
            }

            $this->malarigenoFiscalService->salvarAnalise($contrato, $campanha, [
                'status' => 'Rejeitada',
                'observacoes' => $validated['observacoes'],
            ]);

            $this->malarigenoFiscalService->finalizarAvaliacaoCampanha($contrato, $campanha);

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha rejeitada com sucesso!');

        } catch (\Exception $e) {
            Log::error('MalarigenoCampanhaController@reprovarTudo: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanha,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao reprovar campanha: ' . $e->getMessage()]);
        }
    }

    public function edit($contrato, $produto, $campanhaId)
    {
        $campanha = SgcMalarigeno::with(['modulo', 'fotos', 'anexos'])
            ->where('id_contrato', $contrato)
            ->findOrFail($campanhaId);

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

        $modulos = SgcModulo::query()
            ->select(['id', 'nome', 'nome_planilha_modelo'])
            ->get();

        $empreendimentos = SgcvwEmpreendimentos::where('contrato_id', $contrato)
            ->pluck('cod_emp')
            ->toArray();

        return Inertia::render('Sgc/Contratada/Produtos/Malarigeno/EditCampanha', [
            'campanha' => [
                'id' => $campanha->id,
                'cod_emp' => $campanha->cod_emp,
                'id_campanha' => $campanha->id_campanha,
                'sei_dnit' => $campanha->sei_dnit,
                'subproduto' => $campanha->subproduto,
                'modulo_id' => $campanha->modulo_id,
                'modulo' => $campanha->modulo?->only(['id', 'nome', 'nome_planilha_modelo']),
                'planilha_nome' => $campanha->planilha_nome,
                'planilha_url' => $campanha->planilha_caminho ? Storage::url($campanha->planilha_caminho) : null,
                'fotos' => $campanha->fotos->map(fn($foto) => [
                    'id' => $foto->id,
                    'nome_arquivo' => $foto->nome_arquivo,
                    'url' => $foto->caminho_arquivo ? Storage::url($foto->caminho_arquivo) : null,
                    'latitude' => $foto->latitude,
                    'longitude' => $foto->longitude,
                    'descricao' => $foto->descricao,
                ]),
                'anexos' => $campanha->anexos->map(fn($anexo) => [
                    'id' => $anexo->id,
                    'nome_arquivo' => $anexo->nome_arquivo,
                    'url' => $anexo->caminho_arquivo ? Storage::url($anexo->caminho_arquivo) : null,
                ]),
            ],
            'modulos' => $modulos,
            'empreendimentos' => $empreendimentos,
            'contrato' => $contrato,
            'produto' => $produto,
            'contratos' => ['contratada' => 'Nome da Contratada', 'tipo_contrato' => 'Tipo'],
        ]);
    }

    public function update($contrato, $produto, $campanhaId)
    {
        if (Auth::user()->perfis_id === 3) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Acesso negado. Fiscais não podem atualizar campanhas.']);
        }

        $campanha = SgcMalarigeno::findOrFail($campanhaId);

        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            return redirect()
                ->route('sgc.contratada.produtos.show', [$contrato, $produto, $campanhaId])
                ->withErrors(['error' => 'Apenas campanhas rejeitadas ou em elaboração podem ser atualizadas.']);
        }

        $validated = request()->validate([
            'cod_emp' => 'required|string|max:255',
            'id_campanha' => 'required|integer|min:1',
            'sei_dnit' => 'nullable|string|max:255',
            'subproduto' => 'required|string|max:255',
            'modulo_id' => 'nullable|exists:sgc_modulos,id',
            'arquivo' => 'nullable|file|mimes:xlsx,xls',
            'fotos_remover' => 'nullable|array',
            'anexos_remover' => 'nullable|array',
            'novas_fotos' => 'nullable|array',
            'novos_anexos' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'cod_emp' => $validated['cod_emp'],
                'id_campanha' => $validated['id_campanha'],
                'sei_dnit' => $validated['sei_dnit'] ?? null,
                'subproduto' => $validated['subproduto'],
                'modulo_id' => $validated['modulo_id'],
            ];

            if (isset($validated['arquivo'])) {
                $arquivo = $validated['arquivo'];
                $nomeArquivo = $arquivo->getClientOriginalName();
                $nomeUnico = uniqid() . '_' . $nomeArquivo;
                $caminho = $arquivo->storeAs('Malarigeno/Planilhas', $nomeUnico, 'public');
                $this->sincronizarArquivoPublico($caminho);

                $updateData['planilha_nome'] = $nomeArquivo;
                $updateData['planilha_caminho'] = $caminho;
            }

            if ($campanha->status === 'Rejeitada') {
                $updateData['status'] = 'Em análise';
            }

            $campanha->update($updateData);

            // Remover fotos
            if (!empty($validated['fotos_remover'])) {
                foreach ($validated['fotos_remover'] as $fotoId) {
                    $foto = $campanha->fotos()->find($fotoId);
                    if ($foto) {
                        Storage::delete('public/' . $foto->caminho_arquivo);
                        $this->removerArquivoPublico($foto->caminho_arquivo);
                        $foto->delete();
                    }
                }
            }

            // Remover anexos
            if (!empty($validated['anexos_remover'])) {
                foreach ($validated['anexos_remover'] as $anexoId) {
                    $anexo = $campanha->anexos()->find($anexoId);
                    if ($anexo) {
                        Storage::delete('public/' . $anexo->caminho_arquivo);
                        $this->removerArquivoPublico($anexo->caminho_arquivo);
                        $anexo->delete();
                    }
                }
            }

            // Adicionar novas fotos
            if (!empty(request()->file('novas_fotos'))) {
                foreach (request()->file('novas_fotos') as $fotoFile) {
                    $nomeArquivoF = $fotoFile->getClientOriginalName();
                    $nomeUnico = uniqid() . '_' . $nomeArquivoF;
                    $caminho = $fotoFile->storeAs('Malarigeno/Fotos', $nomeUnico, 'public');
                    $this->sincronizarArquivoPublico($caminho);

                    $campanha->fotos()->create([
                        'nome_arquivo' => $nomeArquivoF,
                        'caminho_arquivo' => $caminho,
                        'latitude' => null,
                        'longitude' => null,
                        'descricao' => null,
                    ]);
                }
            }

            // Adicionar novos anexos
            if (!empty(request()->file('novos_anexos'))) {
                foreach (request()->file('novos_anexos') as $anexoFile) {
                    $nomeArquivoA = $anexoFile->getClientOriginalName();
                    $nomeUnico = uniqid() . '_' . $nomeArquivoA;
                    $caminho = $anexoFile->storeAs('Malarigeno/Anexos', $nomeUnico, 'public');
                    $this->sincronizarArquivoPublico($caminho);

                    $campanha->anexos()->create([
                        'nome_arquivo' => $nomeArquivoA,
                        'caminho_arquivo' => $caminho,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('sgc.contratada.produtos.index', [$contrato, $produto])
                ->with('success', 'Campanha atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MalarigenoCampanhaController@update: erro', [
                'contrato_id' => $contrato,
                'campanha_id' => $campanhaId,
                'erro' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar campanha: ' . $e->getMessage()]);
        }
    }

    private function sincronizarArquivoPublico(string $caminho): void
    {
        try {
            $origem = storage_path('app/public/' . $caminho);

            if (!is_file($origem)) {
                return;
            }

            $destino = public_path('storage/' . $caminho);
            $destinoDir = dirname($destino);

            if (!is_dir($destinoDir)) {
                @mkdir($destinoDir, 0777, true);
            }

            @copy($origem, $destino);
        } catch (\Throwable $e) {
            // Não interrompe atualização por falha na cópia auxiliar.
        }
    }

    private function removerArquivoPublico(?string $caminho): void
    {
        if (!$caminho) {
            return;
        }

        $destino = public_path('storage/' . $caminho);

        if (is_file($destino)) {
            @unlink($destino);
        }
    }

}