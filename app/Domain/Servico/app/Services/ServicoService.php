<?php

namespace App\Domain\Servico\app\Services;

use App\Models\Licenca;
use App\Models\RecursoEquipamento;
use App\Models\RecursoRh;
use App\Models\RecursoVeiculo;
use App\Models\Servicos;
use App\Models\ServicoTema;
use App\Models\ServicoTipo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\DB;

class ServicoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Servicos::class;

    public function listarServicos($contrato, $searchParams): array
    {
        $query = $this->search(...$searchParams)
            ->with([
                'tipo',
                'tema',
//                'status',
                'rhs',
                'veiculos',
                'veiculos.codigo',
                'equipamentos',
                'condicionantes',
                'condicionantes.licenca'
            ])
            ->where('id_contrato', $contrato->id)
            ->where('deleted_at', null);

        return ['servicos' => $query->paginate()->appends($searchParams)];
    }

    public function createServicos($contrato, $servico): array
    {
        $tipos = ServicoTipo::all();
        $temas = ServicoTema::all();
        $rhs = RecursoRh::where('id_contrato', $contrato->id)->get();
        $veiculos = RecursoVeiculo::with(['codigo'])->where('id_contrato', $contrato->id)->get();
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
                'condicionantes.licenca'
            ]);
        }

        return [
            'tipos' => $tipos,
            'temas' => $temas,
            'licencasLi' => $licencasLi,
            'rhs' => $rhs,
            'veiculos' => $veiculos,
            'equipamentos' => $equipamentos,
            'servico' => $servico
        ];
    }

    public function storeServico($request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        return [
            'servico' => $response['model']['id'],
            'request' => $response['request']
        ];
    }

    public function updateServico($request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        return ['request' => $response['request']];
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
                        CONCAT(IF(LENGTH(tl.sigla), tl.sigla, "N/A"), " - ", IF(LENGTH(l.numero_licenca), l.numero_licenca, "N/A"), " - ", IF(LENGTH(c.titulo_condicionante), c.titulo_condicionante, "N/A"), "_", IF(LENGTH(c.descricao), c.descricao, "N/A"))
                    FROM servico_licenca_condicionante AS slc

                    JOIN licencas AS l on slc.id_licenca = l.id
                    JOIN tipo_licencas AS tl on l.tipo = tl.id
                    JOIN condicionantes AS c on slc.id_condicionante = c.id
                    WHERE slc.id_servico = servicos.id
                    ORDER BY slc.id DESC
                    LIMIT 1
                ) as licenca')
        ])
            ->join('programas AS p', 'p.id', '=', 'servicos.servico')
            ->join('temas AS t', 't.id', '=', 'p.cod_tema')
            ->leftJoin('servico_parecer AS sp', 'sp.fk_servico', '=', 'servicos.id')
//            ->when($contratoId, fn($query) => $query->where('id_contrato', $id))
            ->orderBy('servicos.id');

        if ($id) {
            $query->where('servicos.id', $id);
            return $query->first();
        }

//        if (session()->get('auth')['id_perfil'] == '2' || session()->get('auth')['id_perfil'] == '4') {
//            $query->whereIn('status_aprovacao', [2, 3, 4]);
//        }

        return $query->get();
    }
}
