<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Resources;

use App\Models\SgcFaunaModuloAmostral;
use Illuminate\Support\Facades\Storage;

class CampanhaResource
{
    /**
     * Transforma o model SgcFaunaCampanha em array para o Inertia.
     * Substitui o método privado mapCampanhaData() do CampanhaController.
     */
    public static function toArray($campanha): array
    {
        return [
            'id'                      => $campanha->id,
            'id_campanha'             => $campanha->id_campanha,
            'cod_emp'                 => $campanha->cod_emp,
            'familia'                 => $campanha->subproduto,
            'data_campanha_inicial'   => $campanha->data_ini,
            'data_campanha_final'     => $campanha->data_fim,
            'periodo'                 => $campanha->periodo,
            'sei_dnit'                => $campanha->sei_dnit,
            'observacoes'             => $campanha->observacoes,
            'nao_se_aplica'           => $campanha->nao_se_aplica ?? false,
            'status'                  => $campanha->status,
            'formModuloAmostral'      => self::mapModulosManuais($campanha),
            'formPontosAmostragem'    => self::mapPontosQuelonios($campanha),
            'formPontosCavernicola'   => self::mapPontosCavernicola($campanha),
            'formMetodologia'         => self::mapMetodologias($campanha),
            'resultadosTerrestre'     => $campanha->resultadosTerrestre?->toArray() ?? [],
            'resultadosAquatica'      => $campanha->resultadosAquatica?->toArray() ?? [],
            'resultadosCavernicola'   => $campanha->resultadosCavernicola?->toArray() ?? [],
            'consideracoes'                => $campanha->resultados_consideracoes?->consideracoes,
            'planilha_atropelamento'       => $campanha->planilha_atropelamento
                ? \App\Helpers\StorageHelper::publicUrl($campanha->planilha_atropelamento)
                : null,
            'consideracoes_atropelamento'  => $campanha->consideracoes_atropelamento,
            'atropelamento_campanhas'      => self::mapAtropelamentoCampanhas($campanha),
            'abios'                   => self::mapAbios($campanha),
            'profissionais'           => self::mapProfissionais($campanha),
            'modulos_amostrais'       => self::mapModulosAmostrais($campanha),
            'pontos_quelo_crocod'     => self::mapPontosQuelonios($campanha),
            'pontos_cavernicola'      => self::mapPontosCavernicola($campanha),
            'metodologias'            => self::mapMetodologias($campanha),
            'resultados'              => self::mapResultados($campanha),
            'anexos'                  => self::mapAnexos($campanha),
            'analises'                => self::mapAnalises($campanha),
        ];
    }

    // -------------------------------------------------------------------------
    // Mappers privados — cada um responsável por um relacionamento
    // -------------------------------------------------------------------------

    private static function mapModulosManuais($campanha): array
    {
        $modulosManuais = SgcFaunaModuloAmostral::where('campanha_id', $campanha->id)->get();

        return $modulosManuais->map(fn($m) => self::moduloToArray($m))->toArray();
    }

    private static function mapModulosAmostrais($campanha): array
    {
        if ($campanha->modulos_amostrais?->isEmpty()) {
            return [];
        }

        return $campanha->modulos_amostrais->map(fn($m) => self::moduloToArray($m))->toArray();
    }

    private static function moduloToArray($modulo): array
    {
        return [
            'id'                => $modulo->id,
            'data_cadastro'     => $modulo->data_cadastro,
            'tamanho_modulo'    => $modulo->tamanho_modulo,
            'uf'                => $modulo->uf,
            'municipio'         => $modulo->municipio,
            'bioma'             => $modulo->bioma,
            'fitofisionomia'    => $modulo->fitofisionomia,
            'latitude_inicial'  => $modulo->latitude_inicial,
            'longitude_inicial' => $modulo->longitude_inicial,
            'latitude_final'    => $modulo->latitude_final,
            'longitude_final'   => $modulo->longitude_final,
            'obs'               => $modulo->obs,
            'arquivo'           => $modulo->nome_arquivo,
        ];
    }

    private static function mapPontosQuelonios($campanha): array
    {
        if ($campanha->pontos_quelo_crocod?->isEmpty()) {
            return [];
        }

        return $campanha->pontos_quelo_crocod->map(fn($p) => [
            'id'                 => $p->id,
            'ponto_de_coleta'    => $p->ponto_de_coleta,
            'nome_curso_hidrico' => $p->nome_curso_hidrico,
            'latitude'           => $p->latitude,
            'longitude'          => $p->longitude,
            'bacia'              => $p->bacia_hidrografica,
            'profundidade'       => $p->profundidade,
            'largura'            => $p->largura,
            'tipo_substrato'     => $p->tipo_substrato,
        ])->toArray();
    }

    private static function mapPontosCavernicola($campanha): array
    {
        if ($campanha->pontos_cavernicola?->isEmpty()) {
            return [];
        }

        return $campanha->pontos_cavernicola->map(fn($p) => [
            'id'                        => $p->id,
            'cavidade'                  => $p->cavidade,
            'latitude'                  => $p->latitude,
            'longitude'                 => $p->longitude,
            'distancia_eixo_rodovia'    => $p->distancia_eixo_rodovia,
            'formacao_associada'        => $p->formacao_associada,
            'temperatura_media_interna' => $p->temperatura_media_interna,
            'temperatura_media_externa' => $p->temperatura_media_externa,
            'umidade_relativa_interna'  => $p->umidade_relativa_interna,
            'umidade_relativa_externa'  => $p->umidade_relativa_externa,
        ])->toArray();
    }

    private static function mapMetodologias($campanha): array
    {
        if ($campanha->metodologias?->isEmpty()) {
            return [];
        }

        return $campanha->metodologias->map(fn($m) => [
            'id'               => $m->id,
            'grupo_faunistico' => $m->grupo_faunistico,
            'metodologia'      => $m->metodologia,
        ])->toArray();
    }

    private static function mapAbios($campanha): array
    {
        if ($campanha->abios?->isEmpty()) {
            return [];
        }

        return $campanha->abios->map(fn($abio) => [
            'id'     => $abio->id,
            'n_abio' => $abio->n_abio,
            'abio'   => $abio->abio ? [
                'id'               => $abio->abio->id,
                'numero_licenca'   => $abio->abio->numero_licenca,
                'tipo'             => $abio->abio->tipo,
                'data_emissao'     => $abio->abio->data_emissao,
                'vencimento'       => $abio->abio->vencimento,
                'numero_sei'       => $abio->abio->numero_sei,
                'processo_dnit'    => $abio->abio->processo_dnit,
                'inicio_subtrecho' => $abio->abio->inicio_subtrecho,
                'fim_subtrecho'    => $abio->abio->fim_subtrecho,
                'extensao'         => $abio->abio->extensao,
                'emissor'          => $abio->abio->emissor,
                'empreendimento'   => $abio->abio->empreendimento,
                'dias_renovacao'   => $abio->abio->dias_renovacao,
                'renovacao'        => $abio->abio->renovacao,
                'requerimento'     => $abio->abio->requerimento,
                'fiscal'           => $abio->abio->fiscal,
                'obs_renovacao'    => $abio->abio->obs_renovacao,
                'data_publicacao'  => $abio->abio->data_publicacao,
            ] : null,
        ])->toArray();
    }

    // private static function mapProfissionais($campanha): array
    // {
    //     if ($campanha->profissionais?->isEmpty()) {
    //         return [];
    //     }

    //     return $campanha->profissionais->map(fn($prof) => [
    //         'id'               => $prof->id,
    //         'profissional'     => $prof->profissional?->profissional ?? 'N/A',
    //         'grupo_faunistico' => $prof->grupo_faunistico,
    //         'formacao'         => $prof->profissional?->formacao ?? 'N/A',
    //         'funcao'           => $prof->profissional?->funcao ?? 'N/A',
    //         'ctf'              => $prof->profissional?->ctf ?? 'N/A',
    //     ])->toArray();
    // }

    private static function mapProfissionais($campanha): array
    {
        if ($campanha->profissionais?->isEmpty()) {
            return [];
        }

        return $campanha->profissionais->map(fn($prof) => [
            'id'               => $prof->id,
            'grupo_faunistico' => $prof->grupo_faunistico,
            'profissional'     => $prof->profissional ? [
                'id'           => $prof->profissional->id,
                'profissional' => $prof->profissional->profissional,
                'formacao'     => $prof->profissional->formacao,
                'funcao'       => $prof->profissional->funcao,
                'ctf'          => $prof->profissional->ctf,
            ] : null,
        ])->toArray();
    }

    private static function mapResultados($campanha): array
    {
        if ($campanha->resultados?->isEmpty()) {
            return [];
        }

        return $campanha->resultados->map(fn($r) => [
            'id'                          => $r->id,
            'id_campanha'                 => $r->id_campanha,
            'modulo'                      => $r->modulo,
            'parcela'                     => $r->parcela,
            'id_armadilha'                => $r->id_armadilha,
            'grupo_amostrado'             => $r->grupo_amostrado,
            'data_registro'               => $r->data_registro,
            'hora_registro'               => $r->hora_registro,
            'categoria'                   => $r->categoria,
            'classe'                      => $r->classe,
            'ordem'                       => $r->ordem,
            'familia'                     => $r->familia,
            'genero'                      => $r->genero,
            'especie'                     => $r->especie,
            'nome_comum'                  => $r->nome_comum,
            'sexo'                        => $r->sexo,
            'faixa_etaria'                => $r->faixa_etaria,
            'qnt_individuos'              => $r->qnt_individuos,
            'num_marcacao'                => $r->num_marcacao,
            'coletado'                    => $r->coletado,
            'num_tombamento'              => $r->num_tombamento,
            'dados_biometricos'           => $r->dados_biometricos,
            'comp_total'                  => $r->comp_total,
            'cabeca'                      => $r->cabeca,
            'cauda'                       => $r->cauda,
            'femur'                       => $r->femur,
            'orelha'                      => $r->orelha,
            'peso'                        => $r->peso,
            'status_conservacao_federal'  => $r->status_conservacao_federal,
            'status_conservacao_iucn'     => $r->status_conservacao_iucn,
            'especies_bioindicadoras'     => $r->especies_bioindicadoras ?? null,
            'especies_alvo_monitoramento' => $r->especies_alvo_monitoramento ?? null,
        ])->toArray();
    }

    private static function mapAnexos($campanha): array
    {
        if ($campanha->anexos?->isEmpty()) {
            return [];
        }

        return $campanha->anexos->map(fn($anexo) => [
            'id'           => $anexo->id,
            'tipo_anexo'   => $anexo->tipo_anexo,
            'caminho'      => Storage::url($anexo->caminho),
            'nome_arquivo' => $anexo->nome ?? basename($anexo->caminho),
            'created_at'   => $anexo->created_at,
        ])->toArray();
    }

    private static function mapAtropelamentoCampanhas($campanha): array
    {
        if (!$campanha->relationLoaded('atropelamento_campanhas') || $campanha->atropelamento_campanhas?->isEmpty()) {
            return [];
        }

        return $campanha->atropelamento_campanhas->map(fn($ac) => [
            'id'               => $ac->id,
            'rodovia'          => $ac->rodovia,
            'data_inicial'     => $ac->data_inicial,
            'data_final'       => $ac->data_final,
            'uf_inicial'       => $ac->uf_inicial,
            'uf_final'         => $ac->uf_final,
            'km_inicial'       => $ac->km_inicial,
            'km_final'         => $ac->km_final,
            'latitude_inicial' => $ac->latitude_inicial,
            'longitude_inicial'=> $ac->longitude_inicial,
            'latitude_final'   => $ac->latitude_final,
            'longitude_final'  => $ac->longitude_final,
            'obs'              => $ac->obs,
        ])->toArray();
    }

    private static function mapAnalises($campanha): array
    {
        if ($campanha->analises?->isEmpty()) {
            return [];
        }

        return $campanha->analises->map(fn($analise) => [
            'id'         => $analise->id,
            'analise'    => $analise->analise,
            'etapa'      => $analise->etapa,
            'status'     => $analise->status,
            'comentario' => $analise->comentario,
            'fiscal'     => $analise->fiscal ? [  
                'id'   => $analise->fiscal->id,
                'name' => $analise->fiscal->name,
            ] : null,
            'created_at' => $analise->created_at,
        ])->toArray();
    }


}