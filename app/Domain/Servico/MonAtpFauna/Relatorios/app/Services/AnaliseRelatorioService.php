<?php

namespace App\Domain\Servico\MonAtpFauna\Relatorios\app\Services;

use App\Domain\Servico\MonAtpFauna\Resultado\app\Services\ResultadoCampanhaService;
use App\Models\AtFaunaRelatorioAnexo;
use App\Models\AtFaunaResultadoAnalise;
use App\Models\AtFaunaResultadoCampanha;
use App\Models\AtFaunaResultadoOutrasAnalise;
use Illuminate\Support\Facades\DB;

class AnaliseRelatorioService
{
    public function __construct(private readonly ResultadoCampanhaService $resultadoCampanhaService) {}

    public function getAnalise(array $request)
    {
        $dados = [];
        $dados['servico'] = DB::table('servicos')
            ->select([
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
                'p.nome AS servico_nome',
                'status_aprovacao',
                'sp.id AS id_parecer',
                'sp.parecer',
                DB::raw("DATE_FORMAT(sp.created_at, '%d/%m/%Y') as data_parecer"),
                DB::raw("'Serviço' AS tipo"),
                'status_aprovacao AS fk_status',
                't.nome_tema',
                DB::raw(
                    '(
                    SELECT
                        l.numero_licenca
                        FROM servico_licenca_condicionante AS slc
                        JOIN licencas AS l ON slc.id_licenca = l.id
                        JOIN tipo_licencas AS tl ON l.tipo = tl.id
                        JOIN condicionantes AS c ON slc.id_condicionante = c.id
                        WHERE slc.id_servico = servicos.id
                        ORDER BY slc.id DESC
                        LIMIT 1
                    ) as licenca'
                )
            ])
            ->join('programas AS p', 'p.id', '=', 'servicos.servico')
            ->join('temas AS t', 't.id', '=', 'p.cod_tema')
            ->leftJoin('servico_parecer AS sp', 'sp.fk_servico', '=', 'servicos.id')
            ->where('servicos.id', $request['fk_servico'])
            ->orderBy('servicos.id')
            ->first();


        $dados['contrato'] = DB::table('contratos')
            ->select([
                'base_rodovia.rodovia',
                'contratos.numero_contrato',
                'contratos.contratada',
                'estados.uf'
            ])
            ->join('servicos AS s', 's.id_contrato', '=', 'contratos.id')
            ->join('estados', 'estados.id', '=', 'contratos.id_uf')
            ->join('base_rodovia', 'base_rodovia.id', '=', 'contratos.id_rodovia')
            ->where('s.id', $request['fk_servico'])
            ->first();

        $dados['rh'] = DB::table('servico_rh')
            ->select([
                'servico_rh.id',
                DB::raw("(SELECT GROUP_CONCAT(foto.id SEPARATOR ',') FROM rh_arquivo AS foto WHERE rh.id = foto.cod_rh) AS fotos"),
                'rh.*'
            ])
            ->join('rh', 'rh.id', '=', 'servico_rh.id_rh')
            ->where('id_servico', $request['fk_servico'])
            ->get();

        $dados['veiculos'] = DB::table('servico_veiculo')
            ->select([
                'servico_veiculo.id',
                'veiculos.id AS id_veiculo',
                'codv.codigo',
                'codv.descricao',
                'veiculos.obs'
            ])
            ->join('veiculos', 'veiculos.id', '=', 'servico_veiculo.id_veiculo')
            ->join('codigo_veiculos AS codv', 'codv.id', '=', 'veiculos.cod_veiculos')
            ->where('id_servico', $request['fk_servico'])
            ->get();

        $dados['equipamento'] = DB::table('servico_equipamento')
            ->select([
                'servico_equipamento.id',
                'equipamentos.id AS id_equipamento',
                'equipamentos.nome',
                'equipamentos.tipo',
                'equipamentos.numero_interno',
                'equipamentos.modelo',
                'equipamentos.numero_serie',
                'equipamentos.espec_tecnica',
                'equipamentos.obs',
                'equipamentos.numero_patrimonio',
                'equipamentos.cod_sicro',
            ])
            ->join('equipamentos', 'equipamentos.id', '=', 'servico_equipamento.id_equipamento')
            ->where('id_servico', $request['fk_servico'])
            ->get();

        $dados['licenca'] = DB::table('servico_licenca_condicionante')
            ->select([
                'servico_licenca_condicionante.id',
                'servico_licenca_condicionante.vigente',
                'licencas.chave',
                'licencas.numero_licenca',
                'condicionantes.numero',
                'condicionantes.titulo_condicionante',
                'condicionantes.descricao',
            ])
            ->join('licencas', 'licencas.id', '=', 'servico_licenca_condicionante.id_licenca')
            ->join('condicionantes', 'condicionantes.id', '=', 'servico_licenca_condicionante.id_condicionante')
            ->where('id_servico', $request['fk_servico'])
            ->get();

        $dados['campanhas'] = AtFaunaResultadoCampanha::where('fk_resultado', $request['fk_resultado'])->orderBy('fk_campanha')->get();
        $dados['abios_campanha'] = $this->resultadoCampanhaService->getAbiosCampanhas($request['fk_resultado']);
        $dados['abios'] = DB::table('at_fauna_config_vinculacao')
            ->select([
                'at_fauna_config_vinculacao.id',
                'licencas.id as id_licenca',
                'licencas.numero_licenca',
                'licencas.empreendimento',
                'licencas.emissor',
                'licencas_br.extensao_br AS extensao',
                'licencas_br.km_fim',
                'licencas_br.km_inicio',
                'licencas.file_shape AS shapefile_licenca',
                'licencas.chave',
                DB::raw('DATE_FORMAT(licencas.data_emissao, "%d/%m/%Y") as data_emissao'),
                'licencas.vencimento',
                'tipo_licencas.sigla',
                'tipo_licencas.nome as nome_licenca',
                'licencas.processo_dnit',
                'licencas.arquivo_licenca',
                'licencas.fiscal',
                DB::raw('(SELECT id FROM at_fauna_config_vinculacao_ret WHERE fk_at_config_vinculacao = at_fauna_config_vinculacao.id ORDER BY created_at DESC LIMIT 1) as id_ret')
            ])
            ->join('licencas', 'licencas.id', '=', 'at_fauna_config_vinculacao.fk_licenca')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->where('at_fauna_config_vinculacao.fk_servico', $request['fk_servico'])
            ->get();

        $dados['malha_viaria'] = DB::table('servico_licenca_condicionante')
            ->select([
                'amvs.id',
                'servico_licenca_condicionante.id AS fk_servico_licenca',
                'amvs.shapefile',
                'amvs.local_shape',
                'tipo_licencas.sigla',
                'tipo_licencas.nome as nome_licenca',
                'licencas.id as id_licenca',
                'licencas.numero_licenca',
                'licencas.empreendimento',
                'licencas_br.extensao_br AS extensao',
                'licencas_br.km_fim',
                'licencas_br.km_inicio',
                'licencas.chave',
                'licencas.arquivo_licenca',
                'licencas.local_shape AS local_shape_licenca',
                'licencas.geo_json AS geo_json',
                'servico_licenca_condicionante.vigente',
                'br.rodovia',
                'estados.nome as nome_estado',
            ])
            ->join('licencas', 'licencas.id', '=', 'servico_licenca_condicionante.id_licenca')
            ->join('tipo_licencas', 'licencas.tipo', '=', 'tipo_licencas.id')
            ->leftJoin('at_malha_viaria_shapefile AS amvs', 'amvs.fk_servico_licenca', '=', 'servico_licenca_condicionante.id')
            ->leftJoin('licencas_br', 'licencas_br.licenca_id', '=', 'licencas.id')
            ->join('base_rodovia AS br', 'br.rodovia', '=', 'licencas_br.rodovia')
            ->join('estados', 'estados.id', '=', 'licencas_br.uf_inicial')
            ->where('id_servico', $request['fk_servico'])
            // ->where('br.estados_id', '=', 'licencas_br.uf_inicial')
            ->first();
        $dados['analise'] = AtFaunaResultadoAnalise::where('fk_resultado', $request['fk_resultado'])->first();
        $dados['tabela_registro'] = $this->resultadoCampanhaService->getTabelaRegistrosIdentificados($request['fk_resultado']);
        $dados['total_individuos'] = $this->resultadoCampanhaService->getNumeroTotalIndividuos($request['fk_resultado']);
        $dados['atropelados_campanha'] = $this->resultadoCampanhaService->getAtropeladoCampanha($request['fk_resultado']);
        $dados['atropelados_classe'] = $this->resultadoCampanhaService->getAtropeladoClasse($request['fk_resultado']);
        $dados['atropelados_especie'] = $this->resultadoCampanhaService->getAtropeladoEspecie($request['fk_resultado']);
        $dados['atropelados_mes'] = $this->resultadoCampanhaService->getAtropeladoMes($request['fk_resultado']);
        $dados['atropelados_km'] = $this->resultadoCampanhaService->getAtropeladoKm($request['fk_resultado']);
        $dados['outras_analises'] = AtFaunaResultadoOutrasAnalise::where('fk_resultado', $request['fk_resultado'])->get();
        $dados['anexos'] = AtFaunaRelatorioAnexo::where('fk_relatorio', $request['id'])->get();
        $dados['registro_rotografico'] = DB::table('at_fauna_execucao_registro')
            ->select([
                'familia',
                'especie',
                'nome_comum',
                DB::raw('(SELECT
                    id
                    FROM at_fauna_execucao_registro as ater
                    WHERE ater.familia = at_fauna_execucao_registro.familia
                    AND ater.especie = at_fauna_execucao_registro.especie
                    AND ater.nome_comum = at_fauna_execucao_registro.nome_comum
                    LIMIT 1) as id'),
                DB::raw('(SELECT
                    km
                    FROM at_fauna_execucao_registro as ater
                    WHERE ater.familia = at_fauna_execucao_registro.familia
                    AND ater.especie = at_fauna_execucao_registro.especie
                    AND ater.nome_comum = at_fauna_execucao_registro.nome_comum
                    LIMIT 1) as km'),
                DB::raw('(SELECT
                    fotografia
                    FROM at_fauna_execucao_registro as ater
                    WHERE ater.familia = at_fauna_execucao_registro.familia
                    AND ater.especie = at_fauna_execucao_registro.especie
                    AND ater.nome_comum = at_fauna_execucao_registro.nome_comum
                    LIMIT 1) as fotografia')
            ])
            ->join('at_fauna_resultado_campanha AS frc', 'frc.fk_campanha', '=', 'at_fauna_execucao_registro.fk_campanha')
            ->where('frc.fk_resultado', $request['id'])
            ->groupBy('familia', 'especie', 'nome_comum')
            ->get();;

        return $dados;
    }
}
