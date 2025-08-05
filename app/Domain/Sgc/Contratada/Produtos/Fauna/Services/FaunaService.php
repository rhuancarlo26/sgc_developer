<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaProfissionais;
use App\Models\SgcFaunaCavernicola;
use App\Models\SgcFaunaCampanhaProfissional;
use App\Models\SgcFaunaModuloAmostral;
use App\Models\SgcFaunaQuelonios;
use App\Models\SgcFaunaMetodologia;
use App\Models\SgcFaunaResultados;
use App\Models\SgcFaunaResultadosConsideracoes;
use App\Models\SgcFaunaAnexo;
use App\Models\SgcFaunaCampanhaAbios;
use App\Models\SgcFaunaComentarios;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FaunaService
{
    public function salvarCampanha($contratoId, array $data)
    {
        Log::info('FaunaService: Dados recebidos para salvar campanha', [
            'contrato_id' => $contratoId,
            'dados' => array_diff_key($data, array_flip(['anexos', 'planilha'])),
            'anexos' => array_map(function ($file) {
                return $file ? ['name' => $file->getClientOriginalName(), 'size' => $file->getSize()] : null;
            }, $data['anexos'] ?? []),
            'planilha' => $data['planilha'] ? ['name' => $data['planilha']->getClientOriginalName(), 'size' => $data['planilha']->getSize()] : null,
        ]);

        $campanha = SgcFaunaCampanha::create([
            'id_contrato' => $contratoId,
            'id_campanha' => $data['id_campanha'] ?? null,
            'modulos_amostrais' => null,
            'data_ini' => $data['data_campanha_inicial'] ?? null,
            'data_fim' => $data['data_campanha_final'] ?? null,
            'periodo' => $data['periodo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'cod_emp' => $data['cod_emp'] ?? null,
            'subproduto' => $data['subproduto'] ?? null,
            'status' => $data['status'] ?? 'Em análise',
            'versao_analise' => 1,
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
                        'id_contrato' => $contratoId,
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

                if (!empty($moduloData['arquivo']) && $moduloData['arquivo']->isValid()) {
                    $file = $moduloData['arquivo'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('shapefiles', $filename, 'public');
                    $moduloAttributes['nome_arquivo'] = $filename;
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

        // Vincular pontos de fauna cavernícola
        if (!empty($data['pontos_cavernicola']) && !$data['nao_se_aplica']) {
            foreach ($data['pontos_cavernicola'] as $pontoData) {
                SgcFaunaCavernicola::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $campanha->id,
                    'cavidade' => $pontoData['cavidade'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'distancia_eixo_rodovia' => $pontoData['distancia_eixo_rodovia'] ?? null,
                    'formacao_associada' => $pontoData['formacao_associada'] ?? null,
                    'temperatura_media_interna' => $pontoData['temperatura_media_interna'] ?? null,
                    'temperatura_media_externa' => $pontoData['temperatura_media_externa'] ?? null,
                    'umidade_relativa_interna' => $pontoData['umidade_relativa_interna'] ?? null,
                    'umidade_relativa_externa' => $pontoData['umidade_relativa_externa'] ?? null,
                ]);
            }
        }

        // Vincular metodologias
        if (!empty($data['metodologias'])) {
            foreach ($data['metodologias'] as $metodologiaData) {
                SgcFaunaMetodologia::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'grupo_faunistico' => $metodologiaData['grupo_faunistico'],
                    'metodologia' => $metodologiaData['metodologia'],
                ]);
            }
        }

        // Salvar considerações
        if (!empty($data['consideracoes'])) {
            SgcFaunaResultadosConsideracoes::create([
                'id_contrato' => $contratoId,
                'id_campanha' => $campanha->id,
                'consideracoes' => $data['consideracoes'],
            ]);
        }

        // Salvar resultados e atualizar id_campanha
        if (!empty($data['planilha']) && $data['planilha']->isValid()) {
            $result = $this->salvarResultados($contratoId, $data['planilha'], null);
            SgcFaunaResultados::where('id_contrato', $contratoId)
                ->whereNull('id_campanha')
                ->where('created_at', '>=', now()->subSeconds(30))
                ->update(['id_campanha' => $campanha->id]);
        }

        // Salvar anexos
        if (!empty($data['anexos'])) {
            foreach ($data['anexos'] as $tipo => $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('anexos', $filename, 'public');
                    
                    SgcFaunaAnexo::create([
                        'id_contrato' => $contratoId,
                        'id_campanha' => $campanha->id,
                        'tipo_anexo' => $tipo,
                        'nome' => $file->getClientOriginalName(),
                        'caminho' => $path,
                        'versao' => 1,
                    ]);

                    Log::info('FaunaService: Anexo salvo', [
                        'contrato_id' => $contratoId,
                        'campanha_id' => $campanha->id,
                        'tipo_anexo' => $tipo,
                        'nome' => $file->getClientOriginalName(),
                        'caminho' => $path,
                        'versao' => 1,
                    ]);
                }
            }
        }

        Log::info('FaunaService: Campanha salva com ID: ' . $campanha->id);
        return $campanha->id;
    }

    public function salvarProfissional($contratoId, array $data)
    {
        Log::info('FaunaService: Salvando profissional para contrato ID: ' . $contratoId);
        $profissional = SgcFaunaProfissionais::create([
            'id_contrato' => $contratoId,
            'profissional' => $data['profissional'],
            'formacao' => $data['formacao'],
            'telefone' => $data['telefone'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'email' => $data['email'] ?? null,
            'curriculum_lattes' => $data['curriculum_lattes'] ?? null,
            'funcao' => $data['funcao'] ?? null,
            'ctf' => $data['ctf'] ?? null,
            'validade' => $data['validade'] ?? null,
            'conselho_de_classe' => $data['conselho_de_classe'],
            'numero_de_registro' => $data['numero_de_registro'] ?? null,
            'status' => $data['status'],
            'observacao' => $data['observacao'] ?? null,
        ]);
        Log::info('FaunaService: Profissional salvo com ID: ' . $profissional->id);
        return $profissional;
    }

    public function salvarResultados($contratoId, $file, $campanhaId = null)
    {
        Log::info('FaunaService: Processando planilha de resultados para contrato ID: ' . $contratoId . ', campanha ID: ' . ($campanhaId ?? 'não fornecido'));

        try {
            // Deleta resultados antigos associados à campanha
            if ($campanhaId) {
                SgcFaunaResultados::where('id_campanha', $campanhaId)->delete();
                Log::info('FaunaService: Resultados antigos deletados', [
                    'campanha_id' => $campanhaId,
                ]);
            }

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $expectedHeaders = [
                'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
                'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Comum', 'Sexo', 'Faixa Etária',
                'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
                'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN'
            ];

            $headerRow = array_map('trim', array_shift($rows));
            if ($headerRow !== $expectedHeaders) {
                Log::error('FaunaService: Cabeçalho da planilha inválido', [
                    'contrato_id' => $contratoId,
                    'header_row' => $headerRow,
                    'expected_headers' => $expectedHeaders,
                ]);
                throw new \Exception('Cabeçalho da planilha inválido. Use o modelo fornecido.');
            }

            $recordsSaved = 0;
            $recordsSkipped = 0;

            foreach ($rows as $index => $row) {
                $dataRegistro = null;
                if (!empty($row[5])) {
                    try {
                        $dateTime = \DateTime::createFromFormat('d/m/Y', trim($row[5]));
                        if (!$dateTime) {
                            Log::warning('FaunaService: Formato de data inválido na linha ' . ($index + 2), [
                                'contrato_id' => $contratoId,
                                'data' => $row[5],
                            ]);
                            throw new \Exception('Formato de data inválido na linha ' . ($index + 2) . ': ' . $row[5]);
                        }
                        $dataRegistro = $dateTime->format('Y-m-d');
                    } catch (\Exception $e) {
                        Log::error('FaunaService: Erro ao converter data na linha ' . ($index + 2), [
                            'contrato_id' => $contratoId,
                            'data' => $row[5],
                            'erro' => $e->getMessage(),
                        ]);
                        throw $e;
                    }
                }

                $horaRegistro = null;
                if (!empty($row[6])) {
                    try {
                        $dateTime = \DateTime::createFromFormat('H:i', trim($row[6]));
                        if (!$dateTime) {
                            Log::warning('FaunaService: Formato de hora inválido na linha ' . ($index + 2), [
                                'contrato_id' => $contratoId,
                                'hora' => $row[6],
                            ]);
                            throw new \Exception('Formato de hora inválido na linha ' . ($index + 2) . ': ' . $row[6]);
                        }
                        $horaRegistro = $dateTime->format('H:i:s');
                    } catch (\Exception $e) {
                        Log::error('FaunaService: Erro ao converter hora na linha ' . ($index + 2), [
                            'contrato_id' => $contratoId,
                            'hora' => $row[6],
                            'erro' => $e->getMessage(),
                        ]);
                        throw $e;
                    }
                }

                $data = [
                    'id_contrato' => $contratoId,
                    'id_campanha' => $row[0] ?? $campanhaId,
                    'modulo' => $row[1] ?? null,
                    'parcela' => $row[2] ?? null,
                    'id_armadilha' => $row[3] ?? null,
                    'grupo_amostrado' => $row[4] ?? null,
                    'data_registro' => $dataRegistro,
                    'hora_registro' => $horaRegistro,
                    'categoria' => $row[7] ?? null,
                    'classe' => $row[8] ?? null,
                    'ordem' => $row[9] ?? null,
                    'familia' => $row[10] ?? null,
                    'genero' => $row[11] ?? null,
                    'especie' => $row[12] ?? null,
                    'nome_comum' => $row[13] ?? null,
                    'sexo' => $row[14] ?? null,
                    'faixa_etaria' => $row[15] ?? null,
                    'qnt_individuos' => $row[16] ? (int)$row[16] : 0,
                    'num_marcacao' => $row[17] ?? null,
                    'coletado' => $row[18] ?? null,
                    'num_tombamento' => $row[19] ?? null,
                    'dados_biometricos' => $row[20] ?? null,
                    'comp_total' => $row[21] ? (float)$row[21] : null,
                    'cabeca' => $row[22] ? (float)$row[22] : null,
                    'cauda' => $row[23] ? (float)$row[23] : null,
                    'femur' => $row[24] ? (float)$row[24] : null,
                    'orelha' => $row[25] ? (float)$row[25] : null,
                    'peso' => $row[26] ? (float)$row[26] : null,
                    'status_conservacao_federal' => $row[27] ?? null,
                    'status_conservacao_iucn' => $row[28] ?? null,
                ];

                // Removida a verificação de duplicatas, pois resultados antigos foram deletados
                SgcFaunaResultados::create($data);
                $recordsSaved++;
            }

            Log::info('FaunaService: Resultados processados com sucesso', [
                'contrato_id' => $contratoId,
                'campanha_id' => $campanhaId ?? 'não fornecido',
                'registros_salvos' => $recordsSaved,
                'registros_ignorados' => $recordsSkipped,
            ]);

            return [
                'success' => true,
                'message' => 'Resultados salvos com sucesso. ' . $recordsSaved . ' registros salvos, ' . $recordsSkipped . ' registros ignorados.',
            ];
        } catch (\Exception $e) {
            Log::error('FaunaService: Erro ao processar planilha de resultados', [
                'contrato_id' => $contratoId,
                'campanha_id' => $campanhaId ?? 'não fornecido',
                'erro' => $e->getMessage(),
                'linha' => $e->getLine(),
                'arquivo' => $e->getFile(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    public function getProfissionaisByContrato($contratoId)
    {
        return SgcFaunaProfissionais::where('id_contrato', $contratoId)
            ->get(['id', 'profissional', 'formacao'])
            ->toArray();
    }


    public function atualizarCampanha($contrato, $campanhaId, array $data): int
    {
        Log::info('FaunaService: Iniciando atualização da campanha', [
            'campanha_id' => $campanhaId,
            'contrato' => $contrato,
            'data' => array_diff_key($data, array_flip(['anexos', 'planilha'])),
        ]);

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);

        // Atualiza os campos principais da campanha
        $updateData = [
            'id_contrato' => $contrato,
            'cod_emp' => $data['cod_emp'] ?? $campanha->cod_emp,
            'subproduto' => $data['subproduto'] ?? $campanha->subproduto,
            'data_ini' => $data['data_campanha_inicial'] ?? $campanha->data_ini,
            'data_fim' => $data['data_campanha_final'] ?? $campanha->data_fim,
            'periodo' => $data['periodo'] ?? $campanha->periodo,
            'observacoes' => $data['observacoes'] ?? $campanha->observacoes,
            'status' => $data['status'] ?? 'Em análise',
        ];
        Log::info('FaunaService: Dados para atualização da campanha', [
            'campanha_id' => $campanhaId,
            'update_data' => $updateData,
        ]);
        $campanha->update($updateData);

        // Atualiza considerações
        if (isset($data['consideracoes'])) {
            Log::info('FaunaService: Processando considerações', [
                'consideracoes' => $data['consideracoes'],
            ]);
            SgcFaunaResultadosConsideracoes::updateOrCreate(
                ['id_campanha' => $campanha->id, 'id_contrato' => $contrato],
                ['consideracoes' => $data['consideracoes']]
            );
        }

        // Atualiza profissionais
        Log::info('FaunaService: Processando profissionais', [
            'profissionais' => $data['profissionais'] ?? [],
        ]);
        $campanha->profissionais()->delete();
        if (!empty($data['profissionais'])) {
            foreach ($data['profissionais'] as $index => $profissional) {
                Log::info('FaunaService: Processando profissional individual', [
                    'index' => $index,
                    'profissional' => $profissional,
                    'id_profissional' => isset($profissional['id_profissional']) ? (int) $profissional['id_profissional'] : null,
                ]);
                if (isset($profissional['id_profissional']) && $profissional['id_profissional']) {
                    try {
                        SgcFaunaCampanhaProfissional::create([
                            'campanha_id' => $campanha->id,
                            'id_contrato' => $contrato,
                            'profissional_id' => (int) $profissional['id_profissional'],
                            'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                            'formacao' => $profissional['formacao'] ?? null,
                        ]);
                        Log::info('FaunaService: Profissional criado com sucesso', [
                            'profissional_id' => (int) $profissional['id_profissional'],
                            'campanha_id' => $campanha->id,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('FaunaService: Erro ao criar profissional', [
                            'profissional' => $profissional,
                            'erro' => $e->getMessage(),
                            'linha' => $e->getLine(),
                            'arquivo' => $e->getFile(),
                        ]);
                        throw $e;
                    }
                } else {
                    Log::warning('FaunaService: Profissional ignorado devido a id_profissional inválido', [
                        'profissional' => $profissional,
                    ]);
                }
            }
        }
        Log::info('FaunaService: Profissionais atualizados', [
            'profissionais' => $data['profissionais'] ?? [],
        ]);
      

        // Atualiza ABIOs
        Log::info('FaunaService: Processando ABIOs', [
            'id_abio' => $data['id_abio'] ?? [],
        ]);
        $campanha->abios()->delete();
        if (!empty($data['id_abio'])) {
            foreach ($data['id_abio'] as $index => $id) {
                Log::info('FaunaService: Processando ABIO individual', [
                    'index' => $index,
                    'id_abio' => (int) $id,
                ]);
                if ($id) {
                    try {
                        SgcFaunaCampanhaAbios::create([
                            'campanha_id' => $campanha->id,
                            'contrato_id' => $contrato,
                            'n_abio' => (int) $id,
                        ]);
                        Log::info('FaunaService: ABIO criado com sucesso', [
                            'n_abio' => (int) $id,
                            'campanha_id' => $campanha->id,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('FaunaService: Erro ao criar ABIO', [
                            'id_abio' => $id,
                            'erro' => $e->getMessage(),
                            'linha' => $e->getLine(),
                            'arquivo' => $e->getFile(),
                        ]);
                        throw $e;
                    }
                } else {
                    Log::warning('FaunaService: ABIO ignorado devido a id_abio inválido', [
                        'id_abio' => $id,
                    ]);
                }
            }
        }
        Log::info('FaunaService: ABIOs atualizados', [
            'id_abio' => $data['id_abio'] ?? [],
        ]);

        // Atualiza módulos amostrais
        if (isset($data['modulos_amostrais'])) {
            Log::info('FaunaService: Processando módulos amostrais', [
                'modulos_amostrais' => $data['modulos_amostrais'],
            ]);

            // Obtém os IDs dos módulos enviados pelo frontend
            $modulosEnviadosIds = collect($data['modulos_amostrais'])->pluck('id')->filter()->toArray();

            // Deleta os módulos que não estão mais no array enviado
            SgcFaunaModuloAmostral::where('campanha_id', $campanha->id)
                ->whereNotIn('id', $modulosEnviadosIds)
                ->delete();

            foreach ($data['modulos_amostrais'] as $index => $moduloData) {
                Log::info('FaunaService: Processando módulo amostral individual', [
                    'index' => $index,
                    'modulo_data' => $moduloData,
                ]);

                $modulo = array_filter([
                    'id_contrato' => $contrato,
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
                ]);

                $moduloModel = SgcFaunaModuloAmostral::updateOrCreate(
                    [
                        'id' => isset($moduloData['id']) ? (int) $moduloData['id'] : null,
                        'campanha_id' => $campanha->id,
                    ],
                    $modulo
                );

                if (isset($moduloData['arquivo']) && $moduloData['arquivo']) {
                    $path = $moduloData['arquivo']->store('modulos_amostrais');
                    $moduloModel->update(['arquivo' => $path]);
                    Log::info('FaunaService: Arquivo do módulo amostral salvo', [
                        'modulo_id' => $moduloModel->id,
                        'caminho' => $path,
                    ]);
                }

                Log::info('FaunaService: Módulo amostral processado com sucesso', [
                    'modulo_id' => $moduloModel->id,
                    'modulo_data' => $moduloData,
                ]);
            }

            Log::info('FaunaService: Módulos amostrais atualizados', [
                'campanha_id' => $campanha->id,
                'modulos_amostrais' => $data['modulos_amostrais'],
            ]);
        }

        // Atualiza pontos de quelônios/crocodilianos
        if (isset($data['pontos_quelo_crocod'])) {
            Log::info('FaunaService: Processando pontos_quelo_crocod', [
                'pontos_quelo_crocod' => $data['pontos_quelo_crocod'],
            ]);

            // Obtém os IDs dos pontos enviados pelo frontend
            $pontosEnviadosIds = collect($data['pontos_quelo_crocod'])->pluck('id')->filter()->toArray();

            // Deleta os pontos que não estão mais no array enviado
            SgcFaunaQuelonios::where('id_campanha', $campanha->id)
                ->whereNotIn('id', $pontosEnviadosIds)
                ->delete();

            foreach ($data['pontos_quelo_crocod'] as $index => $pontoData) {
                Log::info('FaunaService: Processando ponto quelônio individual', [
                    'index' => $index,
                    'ponto_data' => $pontoData,
                ]);

                $ponto = array_filter([
                    'id_contrato' => $contrato,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    'coordenadas' => $pontoData['coordenadas'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);

                $pontoModel = SgcFaunaQuelonios::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanha->id,
                    ],
                    $ponto
                );

                Log::info('FaunaService: Ponto quelônio processado com sucesso', [
                    'ponto_id' => $pontoModel->id,
                    'ponto_data' => $pontoData,
                ]);
            }

            Log::info('FaunaService: Pontos quelônios atualizados', [
                'campanha_id' => $campanha->id,
                'pontos_quelo_crocod' => $data['pontos_quelo_crocod'],
            ]);
        } else {
            // Se pontos_quelo_crocod não for enviado, deleta todos os pontos
            SgcFaunaQuelonios::where('id_campanha', $campanha->id)->delete();
            Log::info('FaunaService: Todos os pontos quelônios deletados, pois nenhum foi enviado', [
                'campanha_id' => $campanha->id,
            ]);
        }

        // Atualiza pontos de fauna cavernícola
        if (isset($data['pontos_cavernicola'])) {
            Log::info('FaunaService: Processando pontos_cavernicola', [
                'pontos_cavernicola' => $data['pontos_cavernicola'],
            ]);

            // Obtém os IDs dos pontos enviados pelo frontend
            $pontosCavernicolaIds = collect($data['pontos_cavernicola'])->pluck('id')->filter()->toArray();

            // Deleta os pontos que não estão mais no array enviado
            SgcFaunaCavernicola::where('id_campanha', $campanha->id)
                ->whereNotIn('id', $pontosCavernicolaIds)
                ->delete();

            foreach ($data['pontos_cavernicola'] as $index => $pontoData) {
                Log::info('FaunaService: Processando ponto cavernícola individual', [
                    'index' => $index,
                    'ponto_data' => $pontoData,
                ]);

                $ponto = array_filter([
                    'id_contrato' => $contrato,
                    'cavidade' => $pontoData['cavidade'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'distancia_eixo_rodovia' => $pontoData['distancia_eixo_rodovia'] ?? null,
                    'formacao_associada' => $pontoData['formacao_associada'] ?? null,
                    'temperatura_media_interna' => $pontoData['temperatura_media_interna'] ?? null,
                    'temperatura_media_externa' => $pontoData['temperatura_media_externa'] ?? null,
                    'umidade_relativa_interna' => $pontoData['umidade_relativa_interna'] ?? null,
                    'umidade_relativa_externa' => $pontoData['umidade_relativa_externa'] ?? null,
                ]);

                $pontoModel = SgcFaunaCavernicola::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanha->id,
                    ],
                    $ponto
                );

                Log::info('FaunaService: Ponto cavernícola processado com sucesso', [
                    'ponto_id' => $pontoModel->id,
                    'ponto_data' => $pontoData,
                ]);
            }

            Log::info('FaunaService: Pontos cavernícolas atualizados', [
                'campanha_id' => $campanha->id,
                'pontos_cavernicola' => $data['pontos_cavernicola'],
            ]);
        } else {
            // Se pontos_cavernicola não for enviado, deleta todos os pontos
            SgcFaunaCavernicola::where('id_campanha', $campanha->id)->delete();
            Log::info('FaunaService: Todos os pontos cavernícolas deletados, pois nenhum foi enviado', [
                'campanha_id' => $campanha->id,
            ]);
        }

       // Atualiza metodologias
        if (isset($data['metodologias'])) {
            Log::info('FaunaService: Processando metodologias', [
                'metodologias' => $data['metodologias'],
            ]);

            $receivedIds = collect($data['metodologias'])->pluck('id')->filter()->toArray();

            // Deletar metodologias não enviadas
            SgcFaunaMetodologia::where('campanha_id', $campanha->id)
                ->whereNotIn('id', $receivedIds)
                ->delete();

            foreach ($data['metodologias'] as $index => $metodologiaData) {
                Log::info('FaunaService: Processando metodologia individual', [
                    'index' => $index,
                    'metodologia_data' => $metodologiaData,
                ]);

                $metodologia = array_filter([
                    'id_contrato' => $contrato,
                    'grupo_faunistico' => $metodologiaData['grupo_faunistico'] ?? null,
                    'metodologia' => $metodologiaData['metodologia'] ?? null,
                ]);

                $metodologiaModel = SgcFaunaMetodologia::updateOrCreate(
                    [
                        'id' => isset($metodologiaData['id']) ? (int) $metodologiaData['id'] : null,
                        'campanha_id' => $campanha->id,
                    ],
                    $metodologia
                );

                Log::info('FaunaService: Metodologia processada com sucesso', [
                    'metodologia_id' => $metodologiaModel->id,
                    'metodologia_data' => $metodologiaData,
                ]);
            }

            Log::info('FaunaService: Metodologias atualizadas', [
                'campanha_id' => $campanha->id,
                'metodologias' => $data['metodologias'],
            ]);
        } else {
            // Se não houver metodologias enviadas, deletar todas
            SgcFaunaMetodologia::where('campanha_id', $campanha->id)->delete();
            Log::info('FaunaService: Todas as metodologias deletadas, pois nenhuma foi enviada', [
                'campanha_id' => $campanha->id,
            ]);
        }

        // Atualiza anexos
        if (isset($data['anexos'])) {
            Log::info('FaunaService: Processando anexos', [
                'anexos' => array_keys($data['anexos']),
            ]);
            foreach ($data['anexos'] as $tipo => $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('anexos');
                    SgcFaunaAnexo::updateOrCreate(
                        ['id_campanha' => $campanha->id, 'tipo_anexo' => $tipo],
                        [
                            'id_contrato' => $contrato,
                            'nome' => $file->getClientOriginalName(),
                            'caminho' => $path,
                            'versao' => 1,
                        ]
                    );
                }
            }
        }

        // Atualiza planilha
        if (isset($data['planilha']) && $data['planilha'] && $data['planilha']->isValid()) {
            Log::info('FaunaService: Processando planilha', [
                'planilha' => $data['planilha']->getClientOriginalName(),
            ]);
            $path = $data['planilha']->store('planilhas');
            $campanha->update(['planilha' => $path]);
            
            // Chama salvarResultados para processar a planilha
            $this->salvarResultados($contrato, $data['planilha'], $campanha->id);
        }

        Log::info('FaunaService: Campanha atualizada com sucesso', [
            'campanha_id' => $campanha->id,
        ]);

        return $campanha->id;
    }
    
    public function salvarComentario($contratoId, $campanhaId, array $data)
    {
        
        $comentario = SgcFaunaComentarios::create([
            'id_contrato' => $contratoId,
            'campanha_id' => $campanhaId,
            'user_id' => auth()->id(), 
            'etapa' => $data['etapa'],
            'comentario' => $data['comentario'],
        ]);

        Log::info('FaunaService: Comentário salvo com sucesso', [
            'comentario_id' => $comentario->id,
        ]);

        return $comentario;
    }

    public function getComentariosByCampanha($contratoId, $campanhaId)
    {
        return SgcFaunaComentarios::where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->with(['fiscal' => function ($query) {
                $query->select('id', 'name'); // Assumindo que o modelo User tem um campo 'name'
            }])
            ->get()
            ->map(function ($comentario) {
                return [
                    'id' => $comentario->id,
                    'etapa' => $comentario->etapa,
                    'comentario' => $comentario->comentario,
                    'fiscal' => $comentario->fiscal ? $comentario->fiscal->name : 'N/A',
                    'created_at' => $comentario->created_at,
                ];
            })->toArray();
    }


}