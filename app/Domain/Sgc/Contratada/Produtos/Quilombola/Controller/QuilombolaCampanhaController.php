<?php

namespace App\Domain\Sgc\Contratada\Produtos\Quilombola\Controller;

use App\Domain\Sgc\Contratada\Produtos\Quilombola\Requests\StoreQuilombolaSimplificadaRequest;
use App\Domain\Sgc\Contratada\Produtos\Quilombola\Services\QuilombolaEntregaSimplificadaService;
use App\Models\SgcQuilombolaCampanha;
use App\Models\SgcEntregaSimplificada;
use App\Models\SgcModulo;
use App\Models\SgcvwEmpreendimentos;
use App\Models\SgcvwSubprodutos;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class QuilombolaCampanhaController extends Controller
{
    public function __construct(private readonly QuilombolaEntregaSimplificadaService $service) {}

    public function show(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'quilombola', 404);
        [$quilombola, $entrega] = $this->buscar($contrato, $campanha);
        return Inertia::render('Sgc/Contratada/Produtos/Quilombola/VisualizarCampanha', $this->props($quilombola, $entrega));
    }

    public function analise(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'quilombola', 404);
        abort_unless(Auth::user()?->perfis_id === 3, 403);
        [$quilombola, $entrega] = $this->buscar($contrato, $campanha);
        abort_unless($quilombola->status === 'Em análise', 422, 'Campanha não está em análise.');
        return Inertia::render('Sgc/Contratada/Produtos/Quilombola/AnaliseCampanha', $this->props($quilombola, $entrega, true));
    }

    public function edit(int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'quilombola', 404);
        abort_if(Auth::user()?->perfis_id === 3, 403);
        [$quilombola, $entrega] = $this->buscar($contrato, $campanha);
        abort_unless(in_array($quilombola->status, ['Em elaboração', 'Rejeitada']), 422);
        return Inertia::render('Sgc/Contratada/Produtos/Quilombola/EditCampanha', $this->props($quilombola, $entrega, false, true) + $this->dadosFormulario($contrato));
    }

    public function update(Request $request, int $contrato, string $produto, int $campanha)
    {
        abort_unless($produto === 'quilombola', 404);
        abort_if(Auth::user()?->perfis_id === 3, 403);
        [$quilombola, $entrega] = $this->buscar($contrato, $campanha);
        abort_unless(in_array($quilombola->status, ['Em elaboração', 'Rejeitada']), 422);
        $regras = (new StoreQuilombolaSimplificadaRequest())->rules();
        $regras['arquivo'] = 'nullable|file|mimes:xlsx,xls,csv|max:10240';
        $regras['enviar_analise'] = 'nullable|boolean';
        $regras['novas_fotos'] = 'nullable|array';
        $regras['novas_fotos.*.arquivo'] = 'nullable|image|max:10240';
        $regras['novos_anexos'] = 'nullable|array';
        $regras['novos_anexos.*.arquivo'] = 'nullable|file|max:10240';
        $regras['fotos_atualizadas'] = 'nullable|array';
        $dados = $request->validate($regras);
        $dados['enviar_analise'] = $quilombola->status === 'Rejeitada';
        $dados['fotos'] = $request->input('novas_fotos', []);
        foreach ($request->file('novas_fotos', []) as $indice => $foto) $dados['fotos'][$indice]['arquivo'] = $foto['arquivo'] ?? null;
        $dados['anexos'] = [];
        foreach ($request->file('novos_anexos', []) as $anexo) $dados['anexos'][] = ['arquivo' => $anexo['arquivo'] ?? null];
        $this->service->atualizar($quilombola, $entrega, $dados);
        return redirect()->route('sgc.contratada.produtos.quilombola.show', [$contrato, 'quilombola', $campanha])->with('success', 'Campanha QUILOMBOLA atualizada com sucesso.');
    }

    public function aprovar(int $contrato, string $produto, int $campanha)
    {
        return $this->decidir($contrato, $produto, $campanha, 'Aprovada', null);
    }

    public function reprovar(Request $request, int $contrato, string $produto, int $campanha)
    {
        return $this->decidir($contrato, $produto, $campanha, 'Rejeitada', $request->validate(['observacoes' => 'required|string|min:10'])['observacoes']);
    }

    private function decidir(int $contrato, string $produto, int $campanha, string $status, ?string $observacoes)
    {
        abort_unless($produto === 'quilombola' && Auth::user()?->perfis_id === 3, 403);
        [$quilombola, $entrega] = $this->buscar($contrato, $campanha);
        $this->service->registrarAnalise($quilombola, $entrega, $status, $observacoes, Auth::id());
        return redirect()->route('sgc.contratada.produtos.index', [$contrato, 'quilombola'])->with('success', "Campanha {$status} com sucesso.");
    }

    private function buscar(int $contrato, int $campanha): array
    {
        $quilombola = SgcQuilombolaCampanha::with('contrato')->where('id_contrato', $contrato)->findOrFail($campanha);
        $entrega = SgcEntregaSimplificada::where(['id_contrato' => $contrato, 'produto_tipo' => 'quilombola', 'entidade_tipo' => 'quilombola_campanha', 'entidade_id' => $quilombola->id])
            ->with(['modelo', 'fotos', 'anexos', 'analises.fiscal'])->firstOrFail();
        $this->sincronizarArquivosPublicos($entrega);
        return [$quilombola, $entrega];
    }

    private function sincronizarArquivosPublicos(SgcEntregaSimplificada $entrega): void
    {
        $caminhos = collect([$entrega->planilha_caminho])
            ->merge($entrega->fotos->pluck('caminho_arquivo'))
            ->merge($entrega->anexos->pluck('caminho_arquivo'))
            ->filter();
        foreach ($caminhos as $caminho) {
            $origem = storage_path('app/public/'.$caminho);
            $destino = public_path('storage/'.$caminho);
            if (!is_file($origem)) continue;
            if (!is_dir(dirname($destino))) @mkdir(dirname($destino), 0777, true);
            @copy($origem, $destino);
        }
    }

    private function props(SgcQuilombolaCampanha $quilombola, SgcEntregaSimplificada $entrega, bool $analise = false, bool $edicao = false): array
    {
        $ultimaReprovacao = $entrega->analises->where('status', 'Rejeitada')->sortByDesc('created_at')->first();

        return [
            'contrato' => $quilombola->id_contrato, 'produto' => 'quilombola', 'contratos' => $quilombola->contrato ?? ['contratada' => 'Contratada', 'tipo_contrato' => null],
            'canApprove' => Auth::user()?->perfis_id === 3 && $quilombola->status === 'Em análise', 'edicao' => $edicao,
            'campanha' => [
                'id' => $quilombola->id, 'id_campanha' => $quilombola->id_campanha, 'cod_emp' => $quilombola->cod_emp, 'sei_dnit' => $quilombola->sei_dnit,
                'subproduto' => $quilombola->subproduto, 'status' => $quilombola->status, 'versao_analise' => $entrega->versao_analise, 'motivo_reprovacao' => $ultimaReprovacao?->observacoes,
                'modelo_id' => $entrega->modelo_id, 'modulo_id' => $entrega->modelo_id, 'modelo' => $entrega->modelo?->only(['id','nome','nome_planilha_modelo']),
                'planilha_nome' => $entrega->planilha_nome, 'planilha_url' => Storage::url($entrega->planilha_caminho),
                'analises' => $entrega->analises->map(fn ($analise) => [
                    'id' => $analise->id,
                    'versao_analise' => $analise->versao_analise,
                    'status' => $analise->status,
                    'observacoes' => $analise->observacoes,
                    'fiscal' => $analise->fiscal,
                    'created_at' => optional($analise->created_at)->format('d/m/Y H:i'),
                ]),
                'fotos' => $entrega->fotos->map(fn($f) => ['id'=>$f->id,'nome_arquivo'=>$f->nome_arquivo,'url'=>Storage::url($f->caminho_arquivo),'descricao'=>$f->descricao,'latitude'=>$f->latitude,'longitude'=>$f->longitude,'data_captura'=>$f->data_captura]),
                'anexos' => $entrega->anexos->map(fn($a) => ['id'=>$a->id,'nome_arquivo'=>$a->nome_arquivo,'url'=>Storage::url($a->caminho_arquivo)]),
            ],
            'analises' => $entrega->analises->map(fn($a) => ['id'=>$a->id,'versao_analise'=>$a->versao_analise,'status'=>$a->status,'observacoes'=>$a->observacoes,'fiscal'=>$a->fiscal,'created_at'=>optional($a->created_at)->format('d/m/Y H:i')]),
        ];
    }

    private function dadosFormulario(int $contrato): array
    {
        return ['modulos' => SgcModulo::select(['id','nome','nome_planilha_modelo'])->get(), 'empreendimentos' => SgcvwEmpreendimentos::where('contrato_id', $contrato)->pluck('cod_emp')->toArray(), 'subprodutos' => SgcvwSubprodutos::where('contrato_id', $contrato)->where('familia', 'Quilombolas')->get(['id', 'descricao_revisada'])];
    }
}
