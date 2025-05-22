<?php

namespace App\Domain\Sgc\Contratada\RelatorioCoord\Controller;

use App\Domain\Sgc\Contratada\RelatorioCoord\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\SgcComentario;
use App\Models\SgcRelatorioCoordenacao;
use App\Models\SgcRelatorioUpload;
use App\Models\SgcHistoricoRelatorio;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use ZipArchive;


class RelatorioCoordenacaoController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }
  
    public function index(Contrato $contrato, $relatorioNum): Response
    {
        $comentarios = SgcComentario::with(['comment' => function ($query) use ($relatorioNum) {
            $query->where('relatorio_num', $relatorioNum);
        }])
        ->where('contrato_id', $contrato->id)
        ->get();

        $dadosrelat = SgcRelatorioCoordenacao::where('contrato_id', $contrato->id)
            ->where('relatorio_num', $relatorioNum)
            ->get();
            
        $update_anexo = SgcRelatorioUpload::where('contrato_id', $contrato->id)
            ->where('num_relatorio', $relatorioNum)
            ->get()->keyBy('item_id'); 

        return Inertia::render('Sgc/Contratada/Relatorio/Index', [
            'contrato' => $contrato,
            'dadosrelat' => $dadosrelat,
            'update_anexo' => $update_anexo,
            'comentarios' => $comentarios,
            'relatorioNum' => (int) $relatorioNum 
        ]);
    }

    public function relatorios(Contrato $contrato): Response
    {
        $dadosrelat = SgcRelatorioCoordenacao::where('contrato_id', $contrato->id)
            ->with(['historicos' => function($query) {
                $query->select('relatorio_num', 'versao');
            }])
            ->get();

        return Inertia::render('Sgc/Contratada/Relatorio/Relatorios', [
            'contrato' => $contrato,
            'dadosrelat' => $dadosrelat
        ]);
    }

    public function showHistorico($contrato_id, $relatorio_num, $versao): Response
    {
        $historico = SgcHistoricoRelatorio::where('relatorio_num', $relatorio_num)
            ->where('versao', $versao)
            ->get(); 

        $contrato = Contrato::findOrFail($contrato_id);

        return Inertia::render('Sgc/Contratada/Relatorio/Historico', [
            'historico' => $historico,
            'contrato' => $contrato,
        ]);
    }

    public function toggleAprovado(Request $request, $id)
    {
        $item = SgcRelatorioCoordenacao::where('id_item', $id)
            ->where('contrato_id', $request->input('contrato_id'))
            ->where('relatorio_num', $request->input('relatorio_num'))
            ->first();
    
        if ($item) {
            $item->aprovado = $request->input('aprovado');
            $item->save();
            return response()->json(['success' => true, 'aprovado' => $item->aprovado]);
        }
    
        return response()->json(['success' => false, 'message' => 'Item não encontrado'], 404);
    }
    
    public function getDocx($itemId, $contratoId, $versao)
    {
        $documento = SgcRelatorioUpload::where('item_id', $itemId)
            ->where('contrato_id', $contratoId)
            ->where('versao', $versao)
            ->firstOrFail();

        $caminhoCorrigido = 'public/' . str_replace('\\', '/', $documento->caminho);

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

    public function downloadPdfConsolidado($contratoId, $relatorioNum)
    {
        // Busca todos os documentos relacionados ao contrato e relatório
        $documentos = SgcRelatorioUpload::where('contrato_id', $contratoId)
            ->where('num_relatorio', $relatorioNum)
            ->get();

        if ($documentos->isEmpty()) {
            abort(404, 'Nenhum documento encontrado para consolidar.');
        }

        // Gera um HTML para o PDF
        $html = '<h1 style="color: #237D9E;">Relatório Consolidado - Contrato #' . $contratoId . ', Relatório #' . $relatorioNum . '</h1>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; }';
        $html .= 'h2 { color: #333; }';
        $html .= 'table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #f2f2f2; }';
        $html .= 'img { max-width: 100%; height: auto; }'; // Estilo para imagens
        $html .= '</style>';

        foreach ($documentos as $documento) {
            $caminhoCorrigido = 'public/' . str_replace('\\', '/', $documento->caminho);
            if (Storage::exists($caminhoCorrigido)) {
                $filePath = storage_path('app/' . $caminhoCorrigido);
                try {
                    // Carrega o arquivo .docx usando PHPWord
                    $phpWord = IOFactory::load($filePath);
                    $html .= '<div style="margin-bottom: 20px;">';
                    $html .= '<h2>Documento ' . $documento->item_id . ' (Versão ' . $documento->versao . ')</h2>';

                    // Processa cada seção do documento, passando o caminho do arquivo
                    foreach ($phpWord->getSections() as $section) {
                        $html .= $this->convertSectionToHtml($section, $filePath);
                    }

                    $html .= '</div>';
                } catch (\Exception $e) {
                    $html .= '<h2>Documento ' . $documento->item_id . ' (Versão ' . $documento->versao . ')</h2>';
                    $html .= '<p>Erro ao processar o documento: ' . htmlspecialchars($e->getMessage()) . '</p>';
                }
            } else {
                $html .= '<h2>Documento ' . $documento->item_id . ' (Versão ' . $documento->versao . ')</h2>';
                $html .= '<p>Arquivo não encontrado.</p>';
            }
            $html .= '<hr>';
        }

        // Gera o PDF usando DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('relatorio_consolidado_' . $contratoId . '_' . $relatorioNum . '.pdf');
    }

    // Método auxiliar para converter seções do PHPWord para HTML
    private function convertSectionToHtml($section, $filePath)
    {
        $html = '';
        foreach ($section->getElements() as $element) {
            $html .= $this->convertElementToHtml($element, $filePath);
        }
        return $html;
    }

    // Método auxiliar para converter elementos individuais
    private function convertElementToHtml($element, $filePath)
    {
        $html = '';

        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $fontStyle = $element->getFontStyle();
            $style = $fontStyle ? 'style="font-size: ' . ($fontStyle->getSize() ?? 12) . 'pt; font-weight: ' . ($fontStyle->isBold() ? 'bold' : 'normal') . '; font-style: ' . ($fontStyle->isItalic() ? 'italic' : 'normal') . '; color: ' . ($fontStyle->getColor() ?? '#000000') . ';"' : '';
            $html .= '<span ' . $style . '>' . htmlspecialchars($element->getText()) . '</span>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
            foreach ($element->getElements() as $textElement) {
                $html .= $this->convertElementToHtml($textElement, $filePath);
            }
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            $html .= '<table>';
            foreach ($element->getRows() as $row) {
                $html .= '<tr>';
                foreach ($row->getCells() as $cell) {
                    $html .= '<td>';
                    foreach ($cell->getElements() as $cellElement) {
                        $html .= $this->convertElementToHtml($cellElement, $filePath);
                    }
                    $html .= '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</table>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Title) {
            $level = $element->getDepth() + 1;
            $html .= '<h' . $level . '>' . htmlspecialchars($element->getText()) . '</h' . $level . '>';
        } elseif ($element instanceof \PhpOffice\PhpWord\Element\Image) {
            $imageRelationId = $element->getRelationId();
            if ($imageRelationId) {
                $zip = new \ZipArchive();
                if ($zip->open($filePath) === true) {
                    error_log('Abrindo ZIP: ' . $filePath); // Depuração
                    $imagePathInZip = $this->findImageInZip($zip, $imageRelationId);
                    if ($imagePathInZip) {
                        error_log('Imagem encontrada: ' . $imagePathInZip); // Depuração
                        $imageData = base64_encode($zip->getFromName($imagePathInZip));
                        $mimeType = $this->getImageMimeType($imagePathInZip);
                        $html .= '<img src="data:' . $mimeType . ';base64,' . $imageData . '" alt="Imagem">';
                    } else {
                        error_log('Imagem não encontrada para relationId: ' . $imageRelationId); // Depuração
                    }
                    $zip->close();
                } else {
                    error_log('Falha ao abrir ZIP: ' . $filePath); // Depuração
                }
            } else {
                error_log('Nenhum relationId encontrado para a imagem'); // Depuração
            }
        }

        return $html;
    }

    // Método auxiliar para encontrar a imagem no ZIP do .docx
    private function findImageInZip($zip, $relationId)
    {
        $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
        foreach ($imageExtensions as $ext) {
            $potentialPath = 'word/media/' . $relationId . '.' . $ext;
            if ($zip->locateName($potentialPath) !== false) {
                return $potentialPath;
            }
            // Tenta com rId seguido de número (ex.: rId2)
            $potentialPathWithNumber = 'word/media/image' . $relationId . '.' . $ext;
            if ($zip->locateName($potentialPathWithNumber) !== false) {
                return $potentialPathWithNumber;
            }
        }
        // Busca manual na pasta media
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $fileName = $zip->getNameIndex($i);
            if (strpos($fileName, 'word/media/') === 0 && preg_match('/' . preg_quote($relationId, '/') . '/i', $fileName)) {
                return $fileName;
            }
        }
        return false;
    }

    // Método auxiliar para determinar o tipo MIME da imagem
    private function getImageMimeType($filePathInZip)
    {
        $extension = pathinfo($filePathInZip, PATHINFO_EXTENSION);
        return match (strtolower($extension)) {
            'jpeg', 'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            default => 'image/jpeg', // Fallback
        };
    }




}
