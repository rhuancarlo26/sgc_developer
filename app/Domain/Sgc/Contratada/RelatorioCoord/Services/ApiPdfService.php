<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Services;

use Exception;
use Ilovepdf\Ilovepdf;

class ApiPdfService
{
    private Ilovepdf $client;

    public function __construct()
    {
        $this->client = new Ilovepdf(
            'project_public_8eb542f511430e8a74de4886dd88d38a_JGyHU7108bb5572470bdaeaae652962f0edd9',
            'secret_key_a250afe81d699b260fd80ae7ef160fa4_8gGhwd3ea4dbfe383d8d811e3de12ba381b74'
        );
    }

public function converterOfficeParaPdf(string $localFilePath): string
    {
        if (!file_exists($localFilePath)) {
            throw new Exception("Arquivo não encontrado: {$localFilePath}");
        }

        try {
            $task = $this->client->newTask('officepdf');
            $task->addFile($localFilePath);
            $task->execute();

            $diretorioSaida = storage_path('app/public');
            if (!is_dir($diretorioSaida) && !mkdir($diretorioSaida, 0755, true)) {
                throw new Exception("Falha ao criar diretório de saída: {$diretorioSaida}");
            }

            $nomeSaida = pathinfo($localFilePath, PATHINFO_FILENAME) . '_convertido_' . time() . '.pdf';
            $caminhoSaida = $diretorioSaida . DIRECTORY_SEPARATOR . $nomeSaida;

            $task->download($diretorioSaida);

            $arquivos = glob($diretorioSaida . DIRECTORY_SEPARATOR . '*.pdf');
            $arquivoBaixado = end($arquivos);

            if (!$arquivoBaixado || !file_exists($arquivoBaixado)) {
                throw new Exception("Falha ao baixar o PDF convertido.");
            }

            if (!rename($arquivoBaixado, $caminhoSaida)) {
                throw new Exception("Erro ao renomear o arquivo para: {$caminhoSaida}");
            }

            return $caminhoSaida;
        } catch (\Exception $e) {
            throw new Exception("Erro na conversão do arquivo: " . $e->getMessage());
        }
    }

    public function mergePdfs(array $pdfFiles, string $outputDir, string $outputFileName): string
    {
        if (empty($pdfFiles)) {
            throw new Exception("Nenhum arquivo PDF fornecido.");
        }

        try {
            $task = $this->client->newTask('merge');
            foreach ($pdfFiles as $pdfFile) {
                if (!file_exists($pdfFile)) {
                    throw new Exception("Arquivo PDF não encontrado: {$pdfFile}");
                }
                $task->addFile($pdfFile);
            }

            $task->execute();

            if (!is_dir($outputDir) && !mkdir($outputDir, 0755, true)) {
                throw new Exception("Falha ao criar diretório de saída: {$outputDir}");
            }

            $caminhoSaida = $outputDir . DIRECTORY_SEPARATOR . $outputFileName;
            $task->download($outputDir);

            $arquivos = glob($outputDir . DIRECTORY_SEPARATOR . '*.pdf');
            $arquivoBaixado = end($arquivos);

            if (!$arquivoBaixado || !file_exists($arquivoBaixado)) {
                throw new Exception("Falha ao baixar o PDF combinado.");
            }

            if (!rename($arquivoBaixado, $caminhoSaida)) {
                throw new Exception("Erro ao renomear o arquivo combinado para: {$caminhoSaida}");
            }

            return $caminhoSaida;
        } catch (\Exception $e) {
            throw new Exception("Erro no merge dos PDFs: " . $e->getMessage());
        }
    }
}
