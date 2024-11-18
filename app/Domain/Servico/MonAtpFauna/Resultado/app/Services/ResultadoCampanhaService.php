<?php

namespace App\Domain\Servico\MonAtpFauna\Resultado\app\Services;

use App\Models\AtFaunaResultadoCampanha;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use Illuminate\Support\Facades\DB;

class ResultadoCampanhaService extends BaseModelService
{
    use Deletable;

    protected string $modelClass = AtFaunaResultadoCampanha::class;

    public function getTabelaRegistrosIdentificados(int $resultadoId)
    {
        return $this->model
            ->select([
                'fer.especie',
                'fer.nome_comum',
                DB::raw(
                    '(CASE WHEN SUM(fer.n_individuos) IS NOT NULL THEN SUM(fer.n_individuos) ELSE 0 END) AS n_individuos'
                ),
                'fer.federal',
                'fer.iucn'
            ])
            ->join('at_fauna_execucao_campanhas AS fec', 'fec.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro AS fer', 'fer.fk_campanha', '=', 'fec.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->groupBy('fer.especie', 'fer.nome_comum', 'fer.federal', 'fer.iucn')
            ->get();
    }

    public function getNumeroTotalIndividuos($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->sum('at_fauna_execucao_registro.n_individuos');
    }

    public function getAtropeladoCampanha($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->selectRaw('at_fauna_execucao_campanhas.id, COALESCE(SUM(at_fauna_execucao_registro.n_individuos), 0) as n_individuos')
            ->groupBy('at_fauna_execucao_campanhas.id')
            ->get();
    }

    public function getAtropeladoClasse($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->where('n_individuos', '>', 0)
            ->selectRaw('at_fauna_execucao_registro.classe as nome, COALESCE(SUM(at_fauna_execucao_registro.n_individuos), 0) as n_individuos')
            ->groupBy('at_fauna_execucao_registro.classe')
            ->get();
    }

    public function getAtropeladoEspecie($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->whereNotNull('especie')
            ->selectRaw('at_fauna_execucao_registro.classe as nome, COALESCE(SUM(at_fauna_execucao_registro.especie), 0) as n_especies')
            ->groupBy('at_fauna_execucao_registro.classe')
            ->get();
    }

    public function getAtropeladoMes($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->selectRaw("at_fauna_execucao_campanhas.id AS campanha,
            CASE
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 1 THEN 'Janeiro'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 2 THEN 'Fevereiro'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 3 THEN 'Março'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 4 THEN 'Abril'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 5 THEN 'Maio'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 6 THEN 'Junho'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 7 THEN 'Julho'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 8 THEN 'Agosto'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 9 THEN 'Setembro'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 10 THEN 'Outubro'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 11 THEN 'Novembro'
                WHEN MONTH(at_fauna_execucao_registro.data_registro) = 12 THEN 'Dezembro'
            END AS mes,
            COALESCE(SUM(at_fauna_execucao_registro.n_individuos), 0) as n_individuos")
            ->groupBy(DB::raw("at_fauna_execucao_campanhas.id, MONTH(at_fauna_execucao_registro.data_registro)"))
            ->orderBy('at_fauna_execucao_campanhas.id')
            ->orderBy(DB::raw('MONTH(at_fauna_execucao_registro.data_registro)'))
            ->get();
    }

    public function getAtropeladoKm($resultadoId)
    {
        return $this->model
            ->join('at_fauna_execucao_campanhas', 'at_fauna_execucao_campanhas.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_execucao_registro', 'at_fauna_execucao_registro.fk_campanha', '=', 'at_fauna_execucao_campanhas.id')
            ->where('at_fauna_resultado_campanha.fk_resultado', $resultadoId)
            ->selectRaw('at_fauna_execucao_registro.km, COALESCE(SUM(at_fauna_execucao_registro.n_individuos), 0) as n_individuos')
            ->groupBy('at_fauna_execucao_registro.km')
            ->orderBy('at_fauna_execucao_registro.km')
            ->get();
    }

    public function store(array $request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function getResultadoCampanhas($id)
    {
        return $this->model
            ->select([
                'at_fauna_resultado_campanha.id',
                'fec.id AS id_campanha'
            ])
            ->join('at_fauna_execucao_campanhas AS fec', 'fec.id', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->where('at_fauna_resultado_campanha.fk_resultado', $id)
            ->whereNotNull('at_fauna_resultado_campanha.deleted_at')
            ->get();
    }

    public function getAbiosCampanhas($id_resultado)
    {
        return $this->model
            ->select([
                'feca.fk_execucao_campanha as campanha',
                'l.numero_licenca',
                'l.empreendimento',
                'l.emissor',
                DB::raw('DATE_FORMAT(l.data_emissao, "%d/%m/%Y") as data_emissao'),
                'l.vencimento',
                'l.processo_dnit',
                'l.arquivo_licenca',
                'l.fiscal'
            ])
            ->join('at_fauna_execucao_campanha_abio AS feca', 'feca.fk_execucao_campanha', '=', 'at_fauna_resultado_campanha.fk_campanha')
            ->join('at_fauna_config_vinculacao AS fcv', 'fcv.id', '=', 'feca.fk_config_vinculacao')
            ->join('licencas AS l', 'l.id', '=', 'fcv.fk_licenca')
            ->where('at_fauna_resultado_campanha.fk_resultado', $id_resultado)
            ->groupBy('feca.fk_execucao_campanha')
            ->orderBy('feca.fk_execucao_campanha')
            ->get();
    }
}
