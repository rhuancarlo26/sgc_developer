<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\ApiPdfService;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UploadService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SgcRelatorioUpload;
use Illuminate\Http\JsonResponse;
use ZipArchive;
use Illuminate\Support\Str;

class StoreUploadRelatorioController extends Controller
{
    public function __construct(
        private readonly UploadService $uploadService,
        private readonly ApiPdfService $apiPdfService
    ) {}

    public function index(Request $request)
    {
        $response = $this->uploadService->salvarAnexo($request->all());

        return to_route('sgc.contratada.relatorio.detalhes', [
            'contrato' => $request->contrato_id,
            'relatorio_num' => $request->relatorio_num
        ])->with('message', $response['request']);
    }

    public function downloadAnexo($contratoId, $itemId, $relatorioNum)
    {
        $arquivo = SgcRelatorioUpload::where('item_id', $itemId)
            ->where('contrato_id', $contratoId)
            ->where('num_relatorio', $relatorioNum)
            ->firstOrFail();

        // Ajustar o caminho para storage/app/public/
        $caminhoCorrigido = 'public/' . str_replace('\\', '/', $arquivo->caminho);
        dd($caminhoCorrigido);
        if (!Storage::exists($caminhoCorrigido)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $filePath = storage_path('app/' . $caminhoCorrigido);
        $fileName = basename($filePath);
        $fileMimeType = mime_content_type($filePath);

        return response()->streamDownload(function() use ($filePath) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            readfile($filePath);
        }, $fileName, [
            'Content-Type' => $fileMimeType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Content-Length' => filesize($filePath),
            'Pragma' => 'public',
            'Expires' => 0,
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Transfer-Encoding' => 'binary'
        ]);
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return;
        }

        // Escaneia todos os arquivos e subdiretórios
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path); // Recursão para subdiretórios
            } else {
                try {
                    unlink($path);
                } catch (\Exception $e) {
                    $erros[] = "Erro ao processar : " . $e->getMessage();
                }
            }
        }

        // Exclui o diretório vazio
        try {
            rmdir($dir);
        } catch (\Exception $e) {
        }
    }



    public function downloadRelatorioCompleto($contratoId, $relatorioNum)
    {
        // Busca todos os uploads com contrato + relatório
        $arquivos = SgcRelatorioUpload::where('contrato_id', $contratoId)
            ->where('num_relatorio', $relatorioNum)
            ->get();

        if ($arquivos->isEmpty()) {
            return response()->json([
                'message' => 'Nenhum anexo encontrado para esse relatório.',
            ], 404);
        }

        // Agrupa por item_id e seleciona o de maior versão
        $ultimasVersoes = $arquivos->groupBy('item_id')->map(function ($group) {
            return $group->sortByDesc('versao')->first();
        })->sortBy('item_id');

        // Pasta para salvar os PDFs convertidos
        $outputFolder = storage_path('app/public/relatorios/' . $relatorioNum . '_' . Str::uuid());
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0755, true); // Use 0755 em vez de 0777 por segurança
        }

        // Verifica permissões do diretório
        if (!is_writable($outputFolder)) {
            return response()->json([
                'message' => 'O diretório de saída não tem permissões de escrita.',
                'pasta' => $outputFolder,
            ], 500);
        }

        $extensoesOffice = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        $arquivosConvertidos = [];
        $erros = [];

        foreach ($ultimasVersoes as $upload) {
            $caminhoCorrigido = 'public/' . str_replace('\\', '/', $upload->caminho);
            $caminhoAbsoluto = storage_path('app/' . $caminhoCorrigido);

            if (!file_exists($caminhoAbsoluto)) {
                $erros[] = "Arquivo não encontrado: {$upload->nome}";
                continue;
            }

            $ext = strtolower(pathinfo($caminhoAbsoluto, PATHINFO_EXTENSION));
            $nomeBase = pathinfo($upload->nome, PATHINFO_FILENAME);
            $nomeBase = preg_replace('/[^A-Za-z0-9\-]/', '_', $nomeBase);
            $nomePdf = $nomeBase . '.pdf';
            $caminhoSaida = $outputFolder . DIRECTORY_SEPARATOR . $nomePdf;

            try {
                if (in_array($ext, $extensoesOffice)) {
                    // Converte o arquivo Office para PDF usando a API
                    $pdfConvertido = $this->apiPdfService->converterOfficeParaPdf($caminhoAbsoluto);
                    if (file_exists($pdfConvertido)) {
                        if (!rename($pdfConvertido, $caminhoSaida)) {
                            $erros[] = "Erro ao mover o arquivo convertido para {$caminhoSaida}";
                            continue;
                        }
                        $caminhoNormalizado = realpath($caminhoSaida) ?: $caminhoSaida;
                        $arquivosConvertidos[] = $caminhoNormalizado;
                    } else {
                        $erros[] = "Erro: Arquivo convertido não encontrado para {$upload->nome}";
                    }
                } else {
                    if (!copy($caminhoAbsoluto, $caminhoSaida)) {
                        $erros[] = "Erro ao copiar o arquivo {$upload->nome}";
                        continue;
                    }
                    $caminhoNormalizado = realpath($caminhoSaida) ?: $caminhoSaida;
                    $arquivosConvertidos[] = $caminhoNormalizado;
                }
            } catch (\Exception $e) {
                $erros[] = "Erro ao processar {$upload->nome}: " . $e->getMessage();
                continue;
            }
        }

        // Verifica se houve conversão bem-sucedida
        if (empty($arquivosConvertidos)) {
            // Remove a pasta se estiver vazia
            $this->deleteDirectory($outputFolder);
            return response()->json([
                'message' => 'Nenhum arquivo pôde ser convertido ou copiado.',
                'erros' => $erros,
            ], 500);
        }

        try {
            $nomeArquivoCombinado = "relatorio_combinado_{$relatorioNum}_" . time() . '.pdf';
            $arquivoCombinado = $this->apiPdfService->mergePdfs($arquivosConvertidos, $outputFolder, $nomeArquivoCombinado);

            return response()->streamDownload(function () use ($arquivoCombinado, $outputFolder) {
                if (ob_get_level()) {
                    ob_end_clean();
                }
                readfile($arquivoCombinado);
                $this->deleteDirectory($outputFolder);
            }, $nomeArquivoCombinado, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            $this->deleteDirectory($outputFolder);
            return response()->json([
                'message' => 'Erro ao combinar os arquivos PDF.',
                'erros' => array_merge($erros, ["Erro no merge " . $e->getMessage()]),
            ], 500);
        }
    }
}
