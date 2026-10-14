<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\UploadService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Domain\Sgc\Contratada\RelatorioCoord\Services\AnexosRelatorioService;

class StoreUploadRelatorioController extends Controller
{
    public function __construct(private readonly UploadService $uploadService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->uploadService->salvarAnexo($request->all());

        return to_route('sgc.contratada.relatorio.detalhes', [
            'contrato' => $request->contrato_id, 
            'relatorio_num' => $request->relatorio_num
        ])->with('message', $response['request']);
    }

    public function downloadAnexo($contratoId, $itemId, $relatorioNum, Request $request)
    {
        $dados = $request->validate(['versao' => 'sometimes|required|integer|min:0']);
        $arquivo = AnexosRelatorioService::consulta($contratoId, $relatorioNum, $dados['versao'] ?? null)
            ->where('item_id', $itemId)
            ->firstOrFail();

        // Ajustar o caminho para storage/app/public/
        $caminhoCorrigido = 'public/' . str_replace('\\', '/', $arquivo->caminho);
        
        if (!Storage::exists($caminhoCorrigido)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $filePath = Storage::path($caminhoCorrigido);
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

}