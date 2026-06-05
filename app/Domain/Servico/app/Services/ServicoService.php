<?php

namespace App\Domain\Servico\app\Services;

use App\Models\Contrato;
use App\Models\Licenca;
use App\Models\Modulo;
use App\Models\RecursoEquipamento;
use App\Models\RecursoRh;
use App\Models\RecursoVeiculo;
use App\Models\Servicos;
use App\Models\ServicoTema;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ServicoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Servicos::class;

    public function listarServicos(Contrato $contrato, array $searchParams, array $filtros = []): array
    {
        $baseFiltroQuery = Servicos::query()
            ->with([
                'tipo',
                'tema',
            ])
            ->where('id_contrato', $contrato->id)
            ->whereNull('deleted_at');

        $query = $this->search(...$searchParams)
            ->with([
                'tipo',
                'tema',
                'rhs',
                'veiculos',
                'veiculos.codigo',
                'equipamentos',
                'condicionantes',
                'condicionantes.licenca',
            ])
            ->where('id_contrato', $contrato->id)
            ->whereNull('deleted_at')
            ->when($filtros['filtro_tema_id'] ?? null, function ($query, $temaId) {
                $query->where('tema_servico', $temaId);
            })
            ->when($filtros['filtro_servico_id'] ?? null, function ($query, $servicoId) {
                $query->where('servico', $servicoId);
            })
            ->when($filtros['filtro_status_aprovacao'] ?? null, function ($query, $status) {
                $query->where('status_aprovacao', $status);
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $servicos = $query
            ->paginate()
            ->appends([
                ...$searchParams,
                ...$filtros,
            ]);

        $temasFiltro = (clone $baseFiltroQuery)
            ->get()
            ->pluck('tema')
            ->filter()
            ->unique('id')
            ->values()
            ->map(function ($tema) {
                return [
                    'id' => $tema->id,
                    'nome_tema' => $tema->nome_tema,
                ];
            })
            ->values();

        $servicosFiltro = (clone $baseFiltroQuery)
            ->get()
            ->pluck('tipo')
            ->filter()
            ->unique('id')
            ->values()
            ->map(function ($servico) {
                return [
                    'id' => $servico->id,
                    'nome' => $servico->nome,
                ];
            })
            ->values();

        return [
            'servicos' => $servicos,
            'temasFiltro' => $temasFiltro,
            'servicosFiltro' => $servicosFiltro,
            'filtros' => $filtros,
        ];
    }

    public function createServicos($contrato, $servico): array
    {
        $tipos = Modulo::orderBy('nome')->get(['id', 'nome']);
        $temas = ServicoTema::all();

        $rhs = RecursoRh::where('id_contrato', $contrato->id)->get();

        $veiculos = RecursoVeiculo::with(['codigo'])
            ->where('id_contrato', $contrato->id)
            ->get();

        $equipamentos = RecursoEquipamento::where('id_contrato', $contrato->id)->get();

        $licencasLi = Licenca::select(['id', 'numero_licenca'])
            ->with(['condicionantes'])
            ->where('tipo', 6)
            ->get();

        if ($servico) {
            $servico->load([
                'tipo',
                'tema',
                'rhs',
                'veiculos',
                'veiculos.codigo',
                'equipamentos',
                'condicionantes',
                'condicionantes.licenca',
            ]);
        }

        $servicosUsados = Servicos::query()
            ->where('id_contrato', $contrato->id)
            ->whereNull('deleted_at')
            ->when($servico?->id, function ($query) use ($servico) {
                $query->where('id', '!=', $servico->id);
            })
            ->get([
                'id',
                'id_contrato',
                'tema_servico',
                'servico',
            ]);

        return [
            'tipos' => $tipos,
            'temas' => $temas,
            'licencasLi' => $licencasLi,
            'rhs' => $rhs,
            'veiculos' => $veiculos,
            'equipamentos' => $equipamentos,
            'servico' => $servico,
            'servicosUsados' => $servicosUsados,
        ];
    }

    public function storeServico($request): array
    {
        $response = $this->dataManagement->create(
            entity: $this->modelClass,
            infos: $request
        );

        return [
            'servico' => $response['model']['id'],
            'request' => $response['request'],
        ];
    }

    public function updateServico($request): array
    {
        $response = $this->dataManagement->update(
            entity: $this->modelClass,
            infos: $request,
            id: $request['id']
        );

        return [
            'request' => $response['request'],
        ];
    }

    public static function getServicos($id = false, $contratoId = null)
    {
        $query = Servicos::select([
            'servicos.id',
            'servicos.chave',
            'servicos.introducao',
            'servicos.justificativa',
            'servicos.objetivos',
            'servicos.metodologia',
            'servicos.publico_alvo',
            'servicos.tema_servico',
            'servicos.servico',
            'servicos.especificacao',
            'p.nome As servico_nome',
            'status_aprovacao',
            'sp.id as id_parecer',
            'sp.parecer',
            DB::raw("DATE_FORMAT(sp.created_at, '%d/%m/%Y') as data_parecer"),
            DB::raw("'Serviço' AS tipo"),
            DB::raw('status_aprovacao AS fk_status'),
            't.nome_tema',
            DB::raw('(
                SELECT
                    CONCAT(
                        IF(LENGTH(tl.sigla), tl.sigla, "N/A"),
                        " - ",
                        IF(LENGTH(l.numero_licenca), l.numero_licenca, "N/A"),
                        " - ",
                        IF(LENGTH(c.titulo_condicionante), c.titulo_condicionante, "N/A"),
                        "_",
                        IF(LENGTH(c.descricao), c.descricao, "N/A")
                    )
                FROM servico_licenca_condicionante AS slc
                JOIN licencas AS l on slc.id_licenca = l.id
                JOIN tipo_licencas AS tl on l.tipo = tl.id
                JOIN condicionantes AS c on slc.id_condicionante = c.id
                WHERE slc.id_servico = servicos.id
                ORDER BY slc.id DESC
                LIMIT 1
            ) as licenca'),
        ])
            ->join('programas AS p', 'p.id', '=', 'servicos.servico')
            ->join('temas AS t', 't.id', '=', 'p.cod_tema')
            ->leftJoin('servico_parecer AS sp', 'sp.fk_servico', '=', 'servicos.id')
            ->orderBy('servicos.id');

        if ($contratoId) {
            $query->where('servicos.id_contrato', $contratoId);
        }

        if ($id) {
            $query->where('servicos.id', $id);

            return $query->first();
        }

        return $query->get();
    }

    public function buscarModulos(): Collection
    {
        return Modulo::all();
    }

    public function buscarContratos(): Collection
    {
        return Contrato::all();
    }
}
