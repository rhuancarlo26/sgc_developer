<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EtapaApresentacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'data_campanha_inicial'    => 'nullable|date',
            'data_campanha_final'      => 'nullable|date|after_or_equal:data_campanha_inicial',
            'periodo'                  => 'nullable|string|max:255',
            'cod_emp'                  => 'nullable|string|max:255',
            'observacoes'              => 'nullable|string',
            'nao_se_aplica_quelo'      => 'nullable|boolean',
            'nao_se_aplica_cavernicola'=> 'nullable|boolean',

            'id_campanha'             => 'required|integer|between:1,12',

            'id_abio'                  => 'nullable|array',
            'id_abio.*'                => 'nullable|integer',

            'profissionais'                        => 'nullable|array',
            'profissionais.*.profissional'         => 'required_with:profissionais|string|max:255',
            'profissionais.*.grupo_faunistico'     => 'required_with:profissionais|string|in:Avifauna,Herpetofauna,Mastofauna,Ictiofauna,Bentos',

            'modulos_amostrais'                    => 'nullable|array',
            'modulos_amostrais.*.data_cadastro'    => 'nullable|date',
            'modulos_amostrais.*.tamanho_modulo'   => 'nullable|in:1,2,3,4,5',
            'modulos_amostrais.*.uf'               => 'nullable|string|size:2',
            'modulos_amostrais.*.municipio'        => 'nullable|string|max:50',
            'modulos_amostrais.*.bioma'            => 'nullable|string|max:30',
            'modulos_amostrais.*.fitofisionomia'   => 'nullable|string',
            'modulos_amostrais.*.latitude_inicial' => 'nullable|numeric',
            'modulos_amostrais.*.longitude_inicial'=> 'nullable|numeric',
            'modulos_amostrais.*.latitude_final'   => 'nullable|numeric',
            'modulos_amostrais.*.longitude_final'  => 'nullable|numeric',
            'modulos_amostrais.*.arquivo'          => 'nullable|file|mimes:shp,zip|max:1024',

            'pontos_quelo_crocod'                          => 'nullable|array',
            'pontos_quelo_crocod.*.ponto_de_coleta'        => 'nullable|string',
            'pontos_quelo_crocod.*.nome_curso_hidrico'     => 'nullable|string',
            'pontos_quelo_crocod.*.latitude'               => 'nullable|string',
            'pontos_quelo_crocod.*.longitude'              => 'nullable|string',
            'pontos_quelo_crocod.*.bacia'                  => 'nullable|string',
            'pontos_quelo_crocod.*.profundidade'           => 'nullable|numeric',
            'pontos_quelo_crocod.*.largura'                => 'nullable|numeric',
            'pontos_quelo_crocod.*.tipo_substrato'         => 'nullable|string',

            'pontos_cavernicola'                                   => 'nullable|array',
            'pontos_cavernicola.*.cavidade'                        => 'nullable|string',
            'pontos_cavernicola.*.latitude'                        => 'nullable|numeric',
            'pontos_cavernicola.*.longitude'                       => 'nullable|numeric',
            'pontos_cavernicola.*.distancia_eixo_rodovia'          => 'nullable|numeric',
            'pontos_cavernicola.*.formacao_associada'              => 'nullable|string',
            'pontos_cavernicola.*.temperatura_media_interna'       => 'nullable|numeric',
            'pontos_cavernicola.*.temperatura_media_externa'       => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_interna'        => 'nullable|numeric',
            'pontos_cavernicola.*.umidade_relativa_externa'        => 'nullable|numeric',

            'atropelamento_campanha'                   => 'nullable|array',
            'atropelamento_campanha.*.rodovia'          => 'nullable|string',
            'atropelamento_campanha.*.data_inicial'     => 'nullable|date',
            'atropelamento_campanha.*.data_final'       => 'nullable|date',
            'atropelamento_campanha.*.uf_inicial'       => 'nullable|string|size:2',
            'atropelamento_campanha.*.uf_final'         => 'nullable|string|size:2',
            'atropelamento_campanha.*.km_inicial'       => 'nullable|numeric',
            'atropelamento_campanha.*.km_final'         => 'nullable|numeric',
            'atropelamento_campanha.*.obs'              => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id_campanha.required' => 'A campanha é obrigatória.',
            'id_campanha.integer'  => 'A campanha deve ser um número inteiro.',
            'id_campanha.between'  => 'A campanha deve estar entre 1 e 12.',
            'data_campanha_final.after_or_equal' => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
