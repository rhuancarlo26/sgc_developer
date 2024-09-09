<?php

namespace App\Domain\Servico\ContOcorrencia\Relatorio\Controller;

use App\Domain\Servico\ContOcorrencia\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoConOcorrSupervisaoRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoConOcorrSupervisaoRelatorio $relatorio)
    {
        $servico->load([
            'rhs',
            'equipamentos',
            'veiculos.codigo',
            'licencas_condicionantes.condicionante',
            'licencas_condicionantes.licenca.tipo'
        ]);

        $variaveis = $this->relatorioService->getVariaveis($relatorio->resultado);

        $chaves = [
            'graf_reg_intensidade',
            'graf_reg_classificacao',
            'graf_reg_local',
            'graf_reg_lote'
        ];

        foreach ($chaves as $chave) {
            if (isset($variaveis['analise'][$chave])) {
                $variaveis['analise'][$chave . '_imagem'] = $this->showImageAsBase64($variaveis['analise'][$chave]);
            }
        }

        $outrasAnalises = $variaveis['outrasAnalises']->map(function ($item) {
            $item['imagem'] = $this->showImageAsBase64($item->caminho_arquivo);

            return $item;
        });

        if (isset($variaveis['ocorrencias'])) {
            foreach ($variaveis['ocorrencias'] as &$ocorrencia) {
                if (isset($ocorrencia['registros']) && count($ocorrencia['registros']) > 0) {
                    foreach ($ocorrencia['registros'] as &$registro) {
                        if (isset($registro['caminho_arquivo'])) {
                            $registro['caminho_arquivo_imagem'] = $this->showImageAsBase64($registro['caminho_arquivo']);
                        }
                    }
                    unset($registro);
                }
            }
            unset($ocorrencia);
        }

        $pdf = Pdf::loadView('Servico.ConOcorr.Relatorio.RelatorioPdf', compact('contrato', 'servico', 'relatorio', 'variaveis', 'outrasAnalises'));

        return $pdf->stream();
    }

    public function showImageAsBase64($caminho)
    {
        if (Storage::disk('public')->exists($caminho)) {
            $imageContents = Storage::disk('public')->get($caminho);
            $mimeType = Storage::disk('public')->mimeType($caminho);
            $base64Image = base64_encode($imageContents);
            $base64ImageUrl = 'data:' . $mimeType . ';base64,' . $base64Image;

            return $base64ImageUrl;
        } else {
            return null;
        }
    }
}
