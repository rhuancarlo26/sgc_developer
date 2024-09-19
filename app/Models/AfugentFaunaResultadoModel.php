<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class AfugentFaunaResultadoModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'afugent_fauna_resultado';

    protected $guarded = ['id', 'created_at'];

    public function getResultados($id_servico)
    {
        return $this
            ->select([
                'afugent_fauna_resultado.id',
                'afugent_fauna_resultado.nome',
                'afugent_fauna_resultado.periodo',
                DB::raw("DATE_FORMAT(afugent_fauna_resultado.dt_inicio, '%Y-%m-%d') as dt_inicio"),
                DB::raw("DATE_FORMAT(afugent_fauna_resultado.dt_inicio, '%d/%m/%Y') as data_inicio_f"),
                DB::raw("DATE_FORMAT(afugent_fauna_resultado.dt_final, '%Y-%m-%d') as dt_final"),
                DB::raw("DATE_FORMAT(afugent_fauna_resultado.dt_final, '%d/%m/%Y') as data_final_f"),
                'afugent_fauna_relatorio.fk_status AS fk_status_relatorio'
            ])
            ->leftJoin('afugent_fauna_relatorio', 'afugent_fauna_relatorio.id_resultado', '=', 'afugent_fauna_resultado.id')
            ->where('afugent_fauna_resultado.id_servico', $id_servico)
            ->paginate(10);
    }


    public function getRegistrosIdentificados($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                'er.especie',
                'er.nome_comum',
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
                'sc_federal.nome AS nome_federal',
                'sc_federal.sigla AS sigla_federal',
                'sc_iucn.nome AS nome_iucn',
                'sc_iucn.sigla AS sigla_iucn'
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->join('fauna_exec_status_conservacao as sc_federal', 'sc_federal.id', '=', 'er.id_status_conservacao_federal')
            ->join('fauna_exec_status_conservacao as sc_iucn', 'sc_iucn.id', '=', 'er.id_status_conservacao_iucn')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy('er.especie')
            ->groupBy('er.id_status_conservacao_federal')
            ->groupBy('er.id_status_conservacao_iucn')
            ->groupBy('er.nome_comum')
            ->get();
    }

    public function getRegistrosClasse($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                'er.classe',
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy('er.classe')
            ->get();
    }

    public function getRegistrosPeriodo($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                DB::raw('DATE_FORMAT(afugent_fauna_resultado.dt_inicio, "%d/%m/%Y") as dt_inicio'),
                DB::raw('DATE_FORMAT(afugent_fauna_resultado.dt_final, "%d/%m/%Y") as dt_final'),
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->get();
    }

    public function getFormasRegistros($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                'fr.nome',
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->join('afugent_fauna_forma_registro as fr', 'fr.id', '=', 'er.id_forma_registro')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy('er.id_forma_registro')
            ->get();
    }

    public function getRegistrosKm($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                'er.km',
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy('er.km')
            ->orderBy('er.km')
            ->get();
    }

    public function getRegistrosMes($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                DB::raw("(CASE 
                    WHEN MONTH(er.data_registro) = 1 THEN 'Janeiro'
                    WHEN MONTH(er.data_registro) = 2 THEN 'Fevereiro'
                    WHEN MONTH(er.data_registro) = 3 THEN 'Março'
                    WHEN MONTH(er.data_registro) = 4 THEN 'Abril'
                    WHEN MONTH(er.data_registro) = 5 THEN 'Maio'
                    WHEN MONTH(er.data_registro) = 6 THEN 'Junho'
                    WHEN MONTH(er.data_registro) = 7 THEN 'Julho'
                    WHEN MONTH(er.data_registro) = 8 THEN 'Agosto'
                    WHEN MONTH(er.data_registro) = 9 THEN 'Setembro'
                    WHEN MONTH(er.data_registro) = 10 THEN 'Outubro'
                    WHEN MONTH(er.data_registro) = 11 THEN 'Novembro'
                    WHEN MONTH(er.data_registro) = 12 THEN 'Dezembro' END) AS mes"),
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy(DB::raw("MONTH(er.data_registro)"))
            ->orderBy(DB::raw("MONTH(er.data_registro)"))
            ->get();
    }

    public function getRegistrosEspecie($id_resultado, $dt_inicio, $dt_final)
    {
        return $this
            ->select([
                'er.especie',
                DB::raw('(CASE WHEN SUM(er.n_individuos) IS NOT NULL THEN SUM(er.n_individuos) ELSE 0 END) AS n_individuos'),
            ])
            ->join('afugent_fauna_exec_registro as er', 'er.id_servico', '=', 'afugent_fauna_resultado.id_servico')
            ->where('afugent_fauna_resultado.id', $id_resultado)
            ->where('er.data_registro', '>=', $dt_inicio)
            ->where('er.data_registro', '<=', $dt_final)
            ->groupBy('er.especie')
            ->orderBy('er.especie')
            ->get();
    }
}
