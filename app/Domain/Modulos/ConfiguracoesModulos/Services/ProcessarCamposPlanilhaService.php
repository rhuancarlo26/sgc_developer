<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class ProcessarCamposPlanilhaService
{
    public function processarCamposPlanilha(array $data): array
    {
        $arquivo = $data['arquivo'];
        $nomeArquivo = $arquivo->getClientOriginalName();

        try {
            
            $spreadsheet = IOFactory::load(
                $arquivo->getRealPath()
            );

            $sheet = $spreadsheet->getActiveSheet();

            $sheetData = $sheet->toArray(
                null,
                true,
                true,
                false
            );

            $linhaCabecalho = null;

            foreach ($sheetData as $linha) {

                $valoresPreenchidos = array_filter(
                    $linha,
                    fn ($valor) =>
                        $valor !== null &&
                        trim((string) $valor) !== ''
                );

                if (count($valoresPreenchidos) > 0) {
                    $linhaCabecalho = $linha;
                    break;
                }
            }

            if (!$linhaCabecalho) {
                return [
                    'error' => true,
                    'message' => "Não foram encontrados títulos de colunas na planilha {$nomeArquivo}.",
                ];
            }

            $campos = array_map(
                fn ($item) => trim((string) $item),
                $linhaCabecalho
            );

            $campos = array_filter(
                $campos,
                fn ($item) => $item !== ''
            );

            $campos = array_values($campos);

            if (!count($campos)) {
                return [
                    'error' => true,
                    'message' => "Não foram encontrados títulos de colunas na planilha {$nomeArquivo}.",
                ];
            }

            return [
                'error' => false,
                'colunas' => $campos,
            ];

        } catch (Throwable $e) {

            report($e);

            return [
                'error' => true,
                'message' => "Não foi possível ler a planilha {$nomeArquivo}.",
            ];
        }
    }
}
