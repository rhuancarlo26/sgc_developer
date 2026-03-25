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
use App\Models\SgcFaunaAnaliseEtapa;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FaunaService
{
    public function salvarCampanha($contratoId, array $data)
    {

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
        if (!empty($data['pontos_quelo_crocod']) && empty($data['nao_se_aplica_quelo'])) {
            foreach ($data['pontos_quelo_crocod'] as $pontoData) {
                SgcFaunaQuelonios::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $campanha->id,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    // 'coordenadas' => $pontoData['coordenadas'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);
            }
        }

        // Vincular pontos de fauna cavernícola
        if (!empty($data['pontos_cavernicola']) && !$data['nao_se_aplica_cavernicola']) {
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

        // Salvar resultados e considerações
        if (!empty($data['planilha']) && $data['planilha']->isValid()) {
            $this->salvarResultados($contratoId, $data['planilha'], $campanha->id, $data['consideracoes'] ?? null);
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
                    SgcFaunaAnexo::updateOrCreate(
                        [
                            'id_campanha' => $campanha->id,
                            'id_contrato' => $contratoId,
                            'tipo_anexo' => $tipo,
                        ],
                        [
                            'nome' => $file->getClientOriginalName(),
                            'nome' => $filename,
                            'caminho' => $path,
                            'versao' => 1,
                        ]
                    );
                    Log::info('Anexo salvo', [
                        'contrato_id' => $contratoId,
                        'campanha_id' => $campanha->id,
                        'tipo_anexo' => $tipo,
                        'caminho' => $path,
                        'nome' => $filename,
                    ]);
                }
            }
        }

        return $campanha->id;
    }

    public function atualizarParcialCampanha($campanha, $aba, $data)
    {
        switch ($aba) {
            case 'apresentacao':
                $campanha->update([
                    'data_ini' => $data['data_campanha_inicial'] ?? null,
                    'data_fim' => $data['data_campanha_final'] ?? null,
                    'periodo' => $data['periodo'] ?? null,
                    'observacoes' => $data['observacoes'] ?? null,
                    'cod_emp' => $data['cod_emp'] ?? null,
                    'nao_se_aplica_quelo' => $data['nao_se_aplica_quelo'] ?? false,
                    'nao_se_aplica_cavernicola' => $data['nao_se_aplica_cavernicola'] ?? false,
                ]);
                // Atualizar outros dados da aba apresentacao, ex.: profissionais, modulos, etc.
                break;

            case 'metodologia':
                // Atualizar metodologias
                break;

            case 'resultados':
                // Atualizar resultados e considerações
                break;

            case 'anexos':
                // Atualizar anexos
                break;
        }
    }

    public function salvarProfissional($contratoId, array $data)
    {

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

        return $profissional;
    }

    public function salvarResultados($contratoId, $file, $campanhaId = null, $consideracoes = null)
    {
        try {
            // Deleta resultados antigos associados à campanha
            if ($campanhaId) {
                SgcFaunaResultados::where('id_campanha', $campanhaId)->delete();
            }

            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $expectedHeaders = [
                'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
                'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Cientifico','Nome Comum', 'Sexo', 'Faixa Etária',
                'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
                'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN',
                'Espécies Bioindicadoras', 'Espécies Alvo de Monitoramento'
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

            // Preencher a coluna 'ID Campanha' com o campanhaId, se fornecido
            if ($campanhaId) {
                $tempDir = storage_path('app/temp');
                if (!file_exists($tempDir)) {
                    mkdir($tempDir, 0755, true);
                }
                $tempPath = $tempDir . '/updated_planilha_' . time() . '.xlsx';
                $highestRow = $worksheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    $worksheet->setCellValue('A' . $row, $campanhaId);
                }
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($tempPath);
                $spreadsheet = IOFactory::load($tempPath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                array_shift($rows);
                unlink($tempPath);
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
                    'nome_cientifico' => $row[13] ?? null,
                    'nome_comum' => $row[14] ?? null,
                    'sexo' => $row[15] ?? null,
                    'faixa_etaria' => $row[16] ?? null,
                    'qnt_individuos' => $row[17] ? (int)$row[17] : 0,
                    'num_marcacao' => $row[18] ?? null,
                    'coletado' => $row[19] ?? null,
                    'num_tombamento' => $row[20] ?? null,
                    'dados_biometricos' => $row[21] ?? null,
                    'comp_total' => $row[22] ? (float)$row[22] : null,
                    'cabeca' => $row[23] ? (float)$row[23] : null,
                    'cauda' => $row[24] ? (float)$row[24] : null,
                    'femur' => $row[25] ? (float)$row[25] : null,
                    'orelha' => $row[26] ? (float)$row[26] : null,
                    'peso' => $row[27] ? (float)$row[27] : null,
                    'status_conservacao_federal' => $row[28] ?? null,
                    'status_conservacao_iucn' => $row[29] ?? null,
                    'especies_bioindicadoras' => $row[30] ?? null,
                    'especies_alvo_monitoramento' => $row[31] ?? null,
                ];

                SgcFaunaResultados::create($data);
                $recordsSaved++;
            }

            // Salvar considerações, se fornecidas
            if ($campanhaId && $consideracoes) {
                SgcFaunaResultadosConsideracoes::updateOrCreate(
                    ['id_campanha' => $campanhaId, 'id_contrato' => $contratoId],
                    ['consideracoes' => $consideracoes]
                );
            }

            return [
                'success' => true,
                'message' => 'Resultados salvos com sucesso. ' . $recordsSaved . ' registros salvos, ' . $recordsSkipped . ' registros ignorados.',
            ];
        } catch (\Exception $e) {
            Log::error('FaunaService: Erro ao processar planilha de resultados', [
                'contrato_id' => $contratoId,
                'campanha_id' => $campanhaId ?? 'não fornecido',
                'consideracoes' => $consideracoes ?? 'não fornecido',
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
            'versao_analise' => $campanha->versao_analise ?? 1,
        ];

        $campanha->update($updateData);

        // Atualiza considerações
        if (isset($data['consideracoes'])) {

            SgcFaunaResultadosConsideracoes::updateOrCreate(
                ['id_campanha' => $campanha->id, 'id_contrato' => $contrato],
                ['consideracoes' => $data['consideracoes']]
            );
        }

        // Atualiza profissionais
        $campanha->profissionais()->delete();
        if (!empty($data['profissionais'])) {
            foreach ($data['profissionais'] as $index => $profissional) {
                if (isset($profissional['id_profissional']) && $profissional['id_profissional']) {
                    try {
                        SgcFaunaCampanhaProfissional::create([
                            'campanha_id' => $campanha->id,
                            'id_contrato' => $contrato,
                            'profissional_id' => (int) $profissional['id_profissional'],
                            'grupo_faunistico' => $profissional['grupo_faunistico'] ?? null,
                            'formacao' => $profissional['formacao'] ?? null,
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

        // Atualiza ABIOs
        $campanha->abios()->delete();
        if (!empty($data['id_abio'])) {
            foreach ($data['id_abio'] as $index => $id) {
                if ($id) {
                    try {
                        SgcFaunaCampanhaAbios::create([
                            'campanha_id' => $campanha->id,
                            'contrato_id' => $contrato,
                            'n_abio' => (int) $id,
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

        // Atualiza módulos amostrais
        if (isset($data['modulos_amostrais'])) {

            // Obtém os IDs dos módulos enviados pelo frontend
            $modulosEnviadosIds = collect($data['modulos_amostrais'])->pluck('id')->filter()->toArray();

            // Deleta os módulos que não estão mais no array enviado
            SgcFaunaModuloAmostral::where('campanha_id', $campanha->id)
                ->whereNotIn('id', $modulosEnviadosIds)
                ->delete();

            foreach ($data['modulos_amostrais'] as $index => $moduloData) {

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
                }
            }
        }

        // Atualiza pontos de quelônios/crocodilianos
        if (isset($data['pontos_quelo_crocod']) && empty($data['nao_se_aplica_quelo'])) {
            // Obtém os IDs dos pontos enviados pelo frontend
            $pontosEnviadosIds = collect($data['pontos_quelo_crocod'])->pluck('id')->filter()->toArray();

            // Deleta os pontos que não estão mais no array enviado
            SgcFaunaQuelonios::where('id_campanha', $campanha->id)
                ->whereNotIn('id', $pontosEnviadosIds)
                ->delete();

            foreach ($data['pontos_quelo_crocod'] as $index => $pontoData) {
                $ponto = array_filter([
                    'id_contrato' => $contrato,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);

                SgcFaunaQuelonios::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanha->id,
                    ],
                    $ponto
                );
            }
        } else {
            // Se pontos_quelo_crocod não for enviado ou nao_se_aplica_quelo for true, deleta todos os pontos
            SgcFaunaQuelonios::where('id_campanha', $campanha->id)->delete();
        }

        // Atualiza pontos de fauna cavernícola
        if (isset($data['pontos_cavernicola']) && empty($data['nao_se_aplica_cavernicola'])) {
            // Obtém os IDs dos pontos enviados pelo frontend
            $pontosCavernicolaIds = collect($data['pontos_cavernicola'])->pluck('id')->filter()->toArray();

            // Deleta os pontos que não estão mais no array enviado
            SgcFaunaCavernicola::where('id_campanha', $campanha->id)
                ->whereNotIn('id', $pontosCavernicolaIds)
                ->delete();

            foreach ($data['pontos_cavernicola'] as $index => $pontoData) {
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

                SgcFaunaCavernicola::updateOrCreate(
                    [
                        'id' => isset($pontoData['id']) ? (int) $pontoData['id'] : null,
                        'id_campanha' => $campanha->id,
                    ],
                    $ponto
                );
            }
        } else {
            // Se pontos_cavernicola não for enviado ou nao_se_aplica_cavernicola for true, deleta todos os pontos
            SgcFaunaCavernicola::where('id_campanha', $campanha->id)->delete();
        }

       // Atualiza metodologias
        if (isset($data['metodologias'])) {

            $receivedIds = collect($data['metodologias'])->pluck('id')->filter()->toArray();

            // Deletar metodologias não enviadas
            SgcFaunaMetodologia::where('campanha_id', $campanha->id)
                ->whereNotIn('id', $receivedIds)
                ->delete();

            foreach ($data['metodologias'] as $index => $metodologiaData) {

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

            }

        } else {
            // Se não houver metodologias enviadas, deletar todas
            SgcFaunaMetodologia::where('campanha_id', $campanha->id)->delete();

        }

        // Atualiza anexos
        if (isset($data['anexos'])) {
            foreach ($data['anexos'] as $tipo => $file) {
                if ($file && $file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('anexos', $filename, 'public');
                    SgcFaunaAnexo::updateOrCreate(
                        [
                            'id_campanha' => $campanha->id,
                            'id_contrato' => $contrato,
                            'tipo_anexo' => $tipo,
                        ],
                        [
                            'nome' => $file->getClientOriginalName(),
                            'nome' => $filename,
                            'caminho' => $path,
                            'versao' => 1,
                        ]
                    );
                    Log::info('Anexo atualizado', [
                        'contrato_id' => $contrato,
                        'campanha_id' => $campanha->id,
                        'tipo_anexo' => $tipo,
                        'caminho' => $path,
                        'nome' => $filename,
                    ]);
                }
            }
        }

        // Atualiza planilha
        if (isset($data['planilha']) && $data['planilha'] && $data['planilha']->isValid()) {
            $path = $data['planilha']->store('planilhas');
            $campanha->update(['planilha' => $path]);
            $this->salvarResultados($contrato, $data['planilha'], $campanha->id, $data['consideracoes'] ?? null);
        }

        return $campanha->id;
    }

    public function salvarComentario($contratoId, $campanhaId, array $data)
    {
        // Validar os dados recebidos
        $validated = validator($data, [
            'analise_id' => 'required|integer|exists:sgc_fauna_analise_etapas,id',
            'etapa' => 'required|string|in:apresentacao_geral,caracterizacao_area,modulos_amostrais,pontos_quelo_crocod,pontos_cavernicola,metodologia,resultados,anexos',
            'comentario' => 'required|string|max:1000',
            'id_modulo' => 'nullable|integer',
        ])->validate();

        // Buscar a análise para obter o valor de 'analise'
        $analise = SgcFaunaAnaliseEtapa::where('id', $validated['analise_id'])
            ->where('id_campanha', $campanhaId)
            ->firstOrFail();

        // Criar o comentário
        $comentario = SgcFaunaComentarios::create([
            'id_contrato' => $contratoId,
            'campanha_id' => $campanhaId,
            'user_id' => auth()->id(),
            'analise' => $analise->analise,
            'etapa' => $validated['etapa'],
            'comentario' => $validated['comentario'],
            'id_modulo' => $validated['id_modulo'] ?? null,
        ]);

        return $comentario;
    }

    public function excluirComentario($contratoId, $campanhaId, $comentarioId, $userId)
    {
        $comentario = SgcFaunaComentarios::where('id', $comentarioId)
            ->where('id_contrato', $contratoId)
            ->where('campanha_id', $campanhaId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $comentario->delete(); // Soft delete

        return true;
    }

    public function getComentariosByCampanha($contratoId, $campanhaId)
    {
        return SgcFaunaComentarios::where('id_contrato', $contratoId)
            ->where('campanha_id', $campanhaId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($comentario) {
                return [
                    'id' => $comentario->id,
                    'analise' => $comentario->analise,
                    'etapa' => $comentario->etapa,
                    'comentario' => $comentario->comentario,
                    'created_at' => $comentario->created_at,
                ];
            })->toArray();
    }

}