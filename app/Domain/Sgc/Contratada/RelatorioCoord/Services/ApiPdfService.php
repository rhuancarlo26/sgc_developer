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
            // 1. Cria a tarefa
            $task = $this->client->newTask('officepdf');

            // 2. Adiciona o arquivo
            $file = $task->addFile($localFilePath);

            // 3. Processa a conversão
            $task->execute();

            // 4. Define o diretório de saída
            $diretorioSaida = storage_path('app/public');

            // Garante que o diretório existe
            if (!is_dir($diretorioSaida)) {
                mkdir($diretorioSaida, 0755, true);
            }

            if (!is_writable($diretorioSaida)) {
                throw new Exception("O diretório de saída não é gravável: {$diretorioSaida}");
            }
            // 5. Define o nome do arquivo de saída
            $nomeArquivo = pathinfo($localFilePath, PATHINFO_FILENAME);
            $nomeSaida = $nomeArquivo . '_convertido_' . time() . '.pdf';
            $caminhoSaida = $diretorioSaida . DIRECTORY_SEPARATOR . $nomeSaida;

            // 6. Baixa o arquivo PDF convertido para o diretório
            $task->download($diretorioSaida);

            // 7. Verifica se o arquivo foi baixado e renomeia, se necessário
            // O iLovePDF pode gerar um nome de arquivo diferente, então você pode precisar localizá-lo
            $arquivos = glob($diretorioSaida . DIRECTORY_SEPARATOR . '*.pdf');
            $arquivoBaixado = end($arquivos); // Pega o último arquivo PDF baixado

            if (!$arquivoBaixado || !file_exists($arquivoBaixado)) {
                throw new Exception("Nenhum arquivo PDF foi baixado no diretório: {$diretorioSaida}");
            }

            if (!rename($arquivoBaixado, $caminhoSaida)) {
                throw new Exception("Erro ao renomear o arquivo baixado para: {$caminhoSaida}");
            }

            return $caminhoSaida;
        } catch (\Exception $e) {
            throw new Exception("Erro na conversão do arquivo: " . $e->getMessage(), 0, $e);
        }
    }

    public function mergePdfs(array $pdfFiles, string $outputDir, string $outputFileName): string
    {
        if (empty($pdfFiles)) {
            throw new Exception("Nenhum arquivo PDF fornecido para o merge.");
        }

        foreach ($pdfFiles as $index => $pdfFile) {
            if (!file_exists($pdfFile)) {
                throw new Exception("Arquivo PDF não encontrado: {$pdfFile}");
            }
        }

        try {
            $task = $this->client->newTask('merge');

            foreach ($pdfFiles as $index => $pdfFile) {
                $task->addFile($pdfFile);
            }

            $task->execute();

            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            if (!is_writable($outputDir)) {
                throw new Exception("O diretório de saída não é gravável: {$outputDir}");
            }

            $caminhoSaida = $outputDir . DIRECTORY_SEPARATOR . $outputFileName;

            $task->download($outputDir);

            $arquivos = glob($outputDir . DIRECTORY_SEPARATOR . '*.pdf');
            $arquivoBaixado = end($arquivos);

            if (!$arquivoBaixado || !file_exists($arquivoBaixado)) {
                throw new Exception("Nenhum arquivo PDF combinado foi baixado no diretório: {$outputDir}");
            }

            if (!rename($arquivoBaixado, $caminhoSaida)) {
                throw new Exception("Erro ao renomear o arquivo combinado para: {$caminhoSaida}");
            }

            return $caminhoSaida;
        } catch (\Exception $e) {
            throw new Exception("Erro no merge dos arquivos PDF: " . $e->getMessage(), 0, $e);
        }
    }
}
