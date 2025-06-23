<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaProfissionais;
use App\Models\SgcFaunaCampanhaProfissional;
use App\Models\SgcFaunaModuloAmostral;
use App\Models\SgcFaunaQuelonios;
use App\Models\ServicoMonitoraFaunaConfigAbio;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FaunaService
{
    public function salvarCampanha($contratoId, array $data)
    {
        Log::info('FaunaService: Dados recebidos para salvar campanha: ' . json_encode($data));

        $numAbio = null;
        if (!empty($data['id_abio'])) {
            Log::info('FaunaService: Buscando ABIO com ID: ' . $data['id_abio']);
            $abio = ServicoMonitoraFaunaConfigAbio::with('licenca')->find($data['id_abio']);
            if ($abio) {
                Log::info('FaunaService: ABIO encontrado: ' . json_encode($abio->toArray()));
                $numAbio = $abio->licenca->numero_licenca ?? null;
            } else {
                Log::info('FaunaService: ABIO não encontrado para ID: ' . $data['id_abio']);
            }
        }

        $campanha = SgcFaunaCampanha::create([
            'id_contrato' => $contratoId,
            'id_campanha' => $data['id_campanha'] ?? null,
            'modulos_amostrais' => null,
            'data_ini' => $data['data_campanha_inicial'] ?? null,
            'data_fim' => $data['data_campanha_final'] ?? null,
            'periodo' => $data['periodo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'num_abio' => $numAbio,
            'cod_emp' => $data['cod_emp'] ?? null,
            'subproduto' => $data['subproduto'] ?? null,
        ]);

        // Vincular profissionais
        if (!empty($data['profissionais'])) {
            foreach ($data['profissionais'] as $profissionalData) {
                $profissional = SgcFaunaProfissionais::where('id_contrato', $contratoId)
                    ->where('profissional', $profissionalData['profissional'])
                    ->first();
                
                if ($profissional) {
                    SgcFaunaCampanhaProfissional::create([
                        'campanha_id' => $campanha->id,
                        'profissional_id' => $profissional->id,
                        'grupo_faunistico' => $profissionalData['grupo_faunistico'],
                    ]);
                }
            }
        }

        // Vincular módulos amostrais
        if (!empty($data['modulos_amostrais'])) {
            foreach ($data['modulos_amostrais'] as $moduloData) {
                $moduloAttributes = [
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'data_cadastro' => $moduloData['data_cadastro'] ?? null,
                    'tamanho_modulo' => $moduloData['tamanho_modulo'] ?? null,
                    'uf' => $moduloData['uf'] ?? null,
                    'municipio' => $moduloData['municipio'] ?? null,
                    'bioma' => $moduloData['bioma'] ?? null,
                    'fitofisionomia' => $moduloData['fitofisionomia'] ?? null,
                    'latitude_inicial' => $moduloData['latitude_inicial'] ?? null,
                    'longitude_inicial' => $moduloData['longitude_inicial'] ?? null,
                    'latitude_final' => $moduloData['latitude_final'] ?? null,
                    'longitude_final' => $moduloData['longitude_final'] ?? null,
                    'obs' => $moduloData['obs'] ?? null,
                ];

                // Processar upload de shapefile
                if (!empty($moduloData['arquivo'])) {
                    $file = $moduloData['arquivo'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('shapefiles', $filename);
                    $moduloAttributes['nome_arquivo'] = $filename;
                    $moduloAttributes['local_shape'] = $path;
                    $moduloAttributes['shape_file'] = file_get_contents($file->getRealPath());
                }

                SgcFaunaModuloAmostral::create($moduloAttributes);
            }
        }

        // Vincular pontos de quelônios e crocodilianos
        if (!empty($data['pontos_quelo_crocod']) && empty($data['nao_se_aplica'])) {
            foreach ($data['pontos_quelo_crocod'] as $pontoData) {
                SgcFaunaQuelonios::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $campanha->id,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    'coordenadas' => $pontoData['coordenadas'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);
            }
        }

        Log::info('FaunaService: Campanha salva com ID: ' . $campanha->id);
        return $campanha;
    }

    public function salvarProfissional($contratoId, array $data)
    {
        Log::info('FaunaService: Salvando profissional para contrato ID: ' . $contratoId);
        $profissional = SgcFaunaProfissionais::create([
            'id_contrato' => $contratoId,
            'profissional' => $data['profissional'],
            'formacao' => $data['formacao'],
        ]);
        Log::info('FaunaService: Profissional salvo com ID: ' . $profissional->id);
        return $profissional;
    }

    public function getProfissionaisByContrato($contratoId)
    {
        return SgcFaunaProfissionais::where('id_contrato', $contratoId)
            ->get(['id', 'profissional', 'formacao'])
            ->toArray();
    }
}