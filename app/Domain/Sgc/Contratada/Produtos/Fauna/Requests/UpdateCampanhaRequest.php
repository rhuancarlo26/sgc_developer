<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCampanhaRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check() && Auth::user()->perfis_id !== 2;
    }

    public function rules()
    {
        return [
            'data_campanha_inicial' => 'nullable|date',
            'data_campanha_final' => 'nullable|date|after_or_equal:data_campanha_inicial',
            'periodo' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
            'cod_emp' => 'required|string|max:255',
            'subproduto' => 'required|string|max:255',
            'nao_se_aplica_quelo' => 'nullable|boolean',
            'nao_se_aplica_cavernicola' => 'nullable|boolean',
            'abios' => 'nullable|array',
            'profissionais' => 'nullable|array',
            'profissionais.*.grupo_faunistico' => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',
            'profissionais.*.profissional.formacao' => 'nullable|string|max:255',
            'modulos_amostrais' => 'nullable|array',
            'modulos_amostrais.*.id' => 'nullable|integer|exists:sgc_fauna_modulos_amostrais,id',
            'modulos_amostrais.*.data_cadastro' => 'nullable|date',
            'modulos_amostrais.*.tamanho_modulo' => 'nullable|int|max:255',
            'modulos_amostrais.*.uf' => 'nullable|string|max:2',
            'modulos_amostrais.*.municipio' => 'nullable|string|max:255',
            'modulos_amostrais.*.bioma' => 'nullable|string|max:255',
            'modulos_amostrais.*.fitofisionomia' => 'nullable|string',
            'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.latitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_final' => 'nullable|numeric',
            'modulos_amostrais.*.obs' => 'nullable|string',
            'modulos_amostrais.*.arquivo' => 'nullable|file|mimes:shp,zip|max:20480',
            'pontos_quelo_crocod' => 'nullable|array',
            'pontos_quelo_crocod.*.ponto_de_coleta' => 'required_without:nao_se_aplica_quelo|string',
            'pontos_quelo_crocod.*.nome_curso_hidrico' => 'required_without:nao_se_aplica_quelo|string',
            'pontos_quelo_crocod.*.latitude' => 'nullable|string',
            'pontos_quelo_crocod.*.longitude' => 'nullable|string',
            'pontos_quelo_crocod.*.profundidade' => 'nullable|numeric',
            'pontos_quelo_crocod.*.largura' => 'required_without:nao_se_aplica_quelo|numeric',
            'pontos_quelo_crocod.*.tipo_substrato' => 'nullable|string',
            'pontos_cavernicola' => 'nullable|array',
            'pontos_cavernicola.*.cavidade' => 'required_without:nao_se_aplica_cavernicola|string',
            'pontos_cavernicola.*.latitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.longitude' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.distancia_eixo_rodovia' => 'required_without:nao_se_aplica_cavernicola|numeric',
            'pontos_cavernicola.*.formacao_associada' => 'required_without:nao_se_aplica_cavernicola|string',
            'pontos_cavernicola.*.temperatura_media_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.temperatura_media_externa' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_interna' => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_externa' => 'nullable|numeric',
            'metodologias' => 'nullable|array',
            'metodologias.*.grupo_faunistico' => 'nullable|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos,Quelônios e Crocodilianos,Fauna Cavernícola,Invertebrados',
            'metodologias.*.metodologia' => 'nullable|string',
            'consideracoes' => 'nullable|string',

            'planilha_terrestre'   => 'nullable|file|mimes:xlsx,xls|max:10240',
            'planilha_aquatica'    => 'nullable|file|mimes:xlsx,xls|max:10240',
            'planilha_cavernicola' => 'nullable|file|mimes:xlsx,xls|max:10240',


            'anexos.anuencia_proprietarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.registro_fotografico' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.dados_secundarios' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.art' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ret' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.cr' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.ctf' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.anuencia_colecoes' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.oficio_atividades_campo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.rfaef' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'anexos.cartas_anuencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'atropelamento_campanha' => 'nullable|array',
            'atropelamento_campanha.*.rodovia' => 'nullable|string',
            'atropelamento_campanha.*.data_inicial' => 'nullable|date',
            'atropelamento_campanha.*.data_final' => 'nullable|date',
            'atropelamento_campanha.*.uf_inicial' => 'nullable|string|size:2',
            'atropelamento_campanha.*.uf_final' => 'nullable|string|size:2',
            'atropelamento_campanha.*.km_inicial' => 'nullable|numeric',
            'atropelamento_campanha.*.km_final' => 'nullable|numeric',
            'atropelamento_campanha.*.latitude_inicial' => 'nullable|numeric',
            'atropelamento_campanha.*.longitude_inicial' => 'nullable|numeric',
            'atropelamento_campanha.*.latitude_final' => 'nullable|numeric',
            'atropelamento_campanha.*.longitude_final' => 'nullable|numeric',
            'atropelamento_campanha.*.obs' => 'nullable|string',
            'planilha_atropelamento' => 'nullable|file|mimes:xlsx,xls|max:10240',
            'consideracoes_atropelamento' => 'nullable|string',
        ];
    }
}
