<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Services;

class ProcessarCamposPlanilhaService
{
    public function processarCamposPlanilha(array $data): array
    {
        $arquivo = $data['arquivo'];
        $nomeArquivo = $arquivo->getClientOriginalName();

        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($arquivo);

            // pegar aba pelo nome
            $sheet = $spreadsheet->getSheetByName('MODELO');
            // $sheet = $spreadsheet->getSheetByName('Orientações de preenchimento');

            if (!$sheet) {
                return [
                    'error' => true,
                    'message' => "A aba 'MODELO' não foi encontrada na planilha {$nomeArquivo}"
                ];
            }

            $sheetData = $sheet->toArray();
            $arrayColumn = $sheetData[0];
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            // Tratar outros erros do PHPSpreadsheet
            return [
                'error' => true,
                'message' => "Erro ao ler a planilha {$nomeArquivo}"
            ];
        }

        $campos = array_map(fn($item) => trim($item), array_filter($arrayColumn));

        if (!count($campos)) {
            return [
                'error' => true,
                'message' => "Não foram encontradas as colunas do cabeçalho na aba 'MODELO' - planilha {$nomeArquivo}"
            ];
        }

        return [
            'error' => false,
            'colunas' => $campos
        ];
    }
}
