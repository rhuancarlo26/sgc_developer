<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Ponto\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Configuracao\Parametro\Services\ParametroService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaExecCampanha;
use App\Models\SgcPmqaParametroLista;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;


class IndexController extends Controller
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    public function index(
        int $contrato,
        string $produto,
        int $pmqa,
        Request $request
    ): Response {
        $contratoModel = Contrato::findOrFail($contrato);
        $pmqaModel = SgcPmqa::findOrFail($pmqa);

        // 🔹 Pontos
        $pontos = SgcPmqaPonto::where('pmqa_id', $pmqaModel->id)
            ->orderBy('id')
            ->get();

        // 🔹 Parâmetros (via service)
        $searchParams = $request->only(['columns', 'value']);
        $tabParametros = $this->parametroService->index($pmqaModel, $searchParams);

        $vinculacoes = $this->getVinculacoesParaTabela($pmqa);

        $listas = SgcPmqaParametroLista::where('pmqa_id', $pmqa)->get(['id', 'nome', 'medir_iqa']);
        $pontosVinculados = SgcPmqaPonto::where('pmqa_id', $pmqa)->get(['id', 'nome_ponto_coleta']);

        $campanhaTermo = $request->query('campanha'); // ex: ?campanha=teste

        $campanhas = SgcPmqaExecCampanha::query()
            ->with(['pontos']) // traz os pontos de cada campanha
            ->where('pmqa_id', $pmqaModel->id)
            ->when($campanhaTermo, function ($q) use ($campanhaTermo) {
                $q->where('nome_campanha', 'like', "%{$campanhaTermo}%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->appends($request->query());

        return Inertia::render('Sgc/Contratada/Produtos/Pmqa/Create', [
            'contrato'  => $contratoModel->id,
            'contratos' => $contratoModel,
            'produto'   => ['slug' => $produto],
            'pmqa'      => $pmqaModel,
            'servico'   => $pmqaModel, // 👈 importante para IndexParametros / modal

            'pontos'    => $pontos,

            ...$tabParametros,

            'subStep'   => (int) $request->query('subStep', 2),
            'tab'       => $request->query('tab', 'configuracao'),
            'vinculacoes' => $vinculacoes,
            'pontosVinculados' => $pontosVinculados,
            'campanhas' => $campanhas,
        ]);
    }


    private function getVinculacoesParaTabela(int $pmqaId)
    {
        $rows = DB::table('sgc_pmqa_parametros_lista as l')
            ->join('sgc_pmqa_config_ponto_lista as pl', 'pl.lista_id', '=', 'l.id')
            ->join('sgc_pmqa_pontos as p', 'p.id', '=', 'pl.ponto_id')
            ->where('l.pmqa_id', $pmqaId)
            ->where('pl.pmqa_id', $pmqaId)
            ->orderBy('l.id')
            ->orderBy('p.id')
            ->get([
                'l.id as id',
                'l.nome as nome',
                'l.medir_iqa as medir_iqa',
                'p.id as ponto_id',
                'p.nome_ponto_coleta as nome_ponto_coleta',
                'p.classe as classe',
                'p.tipo_ambiente as tipo_ambiente',
                'p.uf',
                'p.municipio',
                'p.bacia_hidrografica',
                'p.km_rodovia',
                'p.estaca',
                'p.lat_x',
                'p.long_y',
            ]);

        // 🔹 Agrupa por lista
        $grouped = [];
        foreach ($rows as $r) {
            if (!isset($grouped[$r->id])) {
                $grouped[$r->id] = [
                    'id' => (int) $r->id,
                    'nome' => $r->nome,
                    'medir_iqa' => $r->medir_iqa,
                    'pontos' => [],
                ];
            }

            $grouped[$r->id]['pontos'][] = [
                'id' => (int) $r->ponto_id,
                'nome_ponto_coleta' => $r->nome_ponto_coleta,
                'classe' => $r->classe,
                'tipo_ambiente' => $r->tipo_ambiente,
                'uf' => $r->uf,
                'municipio' => $r->municipio,
                'bacia_hidrografica' => $r->bacia_hidrografica,
                'km_rodovia' => $r->km_rodovia,
                'estaca' => $r->estaca,
                'lat_x' => $r->lat_x,
                'long_y' => $r->long_y
            ];
        }

        $items = array_values($grouped);

        // 🔹 Paginação manual (padrão Laravel)
        $perPage = 15;
        $page = request()->get('page', 1);
        $total = count($items);

        return new LengthAwarePaginator(
            array_slice($items, ($page - 1) * $perPage, $perPage),
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }
}
