<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaResultados;
use App\Models\SgcFaunaResultadosConsideracoes;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultadoService
{
    public function salvarResultados($contratoId, $file, $campanhaId = null, $consideracoes = null)
    {
        try {
            $recordsSaved = 0;
            $recordsSkipped = 0;

            if ($file && $file->isValid()) {
                if ($campanhaId) {
                    SgcFaunaResultados::where('id_campanha', $campanhaId)->delete();
                }

                $spreadsheet = IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();

                $expectedHeaders = [
                    'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
                    'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Comum', 'Sexo', 'Faixa Etária',
                    'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
                    'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN',
                    'Espécies Bioindicadoras', 'Espécies Alvo de Monitoramento'
                ];

                $headerRow = array_map('trim', array_shift($rows));
                if ($headerRow !== $expectedHeaders) {
                    Log::error('ResultadoService: Cabeçalho da planilha inválido', [
                        'contrato_id' => $contratoId,
                        'header_row' => $headerRow,
                        'expected_headers' => $expectedHeaders,
                    ]);
                    throw new \Exception('Cabeçalho da planilha inválido. Use o modelo fornecido.');
                }

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

                foreach ($rows as $index => $row) {
                    $dataRegistro = null;
                    if (!empty($row[5])) {
                        try {
                            $dateTime = \DateTime::createFromFormat('d/m/Y', trim($row[5]));
                            if (!$dateTime) {
                                Log::warning('ResultadoService: Formato de data inválido na linha ' . ($index + 2), [
                                    'contrato_id' => $contratoId,
                                    'data' => $row[5],
                                ]);
                                throw new \Exception('Formato de data inválido na linha ' . ($index + 2) . ': ' . $row[5]);
                            }
                            $dataRegistro = $dateTime->format('Y-m-d');
                        } catch (\Exception $e) {
                            Log::error('ResultadoService: Erro ao converter data na linha ' . ($index + 2), [
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
                                Log::warning('ResultadoService: Formato de hora inválido na linha ' . ($index + 2), [
                                    'contrato_id' => $contratoId,
                                    'hora' => $row[6],
                                ]);
                                throw new \Exception('Formato de hora inválido na linha ' . ($index + 2) . ': ' . $row[6]);
                            }
                            $horaRegistro = $dateTime->format('H:i:s');
                        } catch (\Exception $e) {
                            Log::error('ResultadoService: Erro ao converter hora na linha ' . ($index + 2), [
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
                        'especies_bioindicadoras' => $row[29] ?? null,
                        'especies_alvo_monitoramento' => $row[30] ?? null,
                    ];

                    SgcFaunaResultados::create($data);
                    $recordsSaved++;
                }
            }

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
            Log::error('ResultadoService: Erro ao processar planilha de resultados', [
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
}