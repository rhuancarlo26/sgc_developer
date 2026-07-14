<?php

namespace App\Domain\Servico\PassagemFauna\Relatorio\Controller;

use App\Domain\Servico\PassagemFauna\Relatorio\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoPassagemFaunaRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function __construct(private readonly RelatorioService $relatorioService)
    {
    }

    public function index(Contrato $contrato, Servicos $servico, ServicoPassagemFaunaRelatorio $relatorio)
    {
        $servico->load([
            'rhs',
            'equipamentos',
            'veiculos.codigo',
            'licencas_condicionantes.licenca',
            'licencas_condicionantes.condicionante'
        ]);

        $relatorio->load([
            'resultado.analise',
            'resultado.outras_analises',
            'resultado.campanhas.abios.abio.licenca.tipo_rel',
            'resultado.campanhas.registros.status_iunc',
            'resultado.campanhas.registros.status_federal',
        ]);

        $analises = [
            'graf_reg_classe' => $this->showImageAsBase64($relatorio->resultado->analise->graf_reg_classe),
            'graf_reg_campanha' => $this->showImageAsBase64($relatorio->resultado->analise->graf_reg_campanha),
            'graf_reg_especie' => $this->showImageAsBase64($relatorio->resultado->analise->graf_reg_especie),
            'graf_reg_passagem' => $this->showImageAsBase64($relatorio->resultado->analise->graf_reg_passagem),
            'graf_reg_tipo' => $this->showImageAsBase64($relatorio->resultado->analise->graf_reg_tipo)
        ];

        $outrasAnalises = $relatorio->resultado->outras_analises->map(function ($item) {
            $item['imagem'] = $this->showImageAsBase64($item->caminho_arquivo);

            return $item;
        });

        $pdf = Pdf::loadView('Servico.PassagemFauna.Relatorio.RelatorioPdf', compact('contrato', 'servico', 'relatorio', 'analises', 'outrasAnalises'));

        return $pdf->stream();
    }

    public function showImageAsBase64($caminho)
    {
        if (\Storage::disk('public')->exists($caminho)) {
            $imageContents = \Storage::disk('public')->get($caminho);
            $mimeType = \Storage::disk('public')->mimeType($caminho);
            $base64Image = base64_encode($imageContents);
            $base64ImageUrl = 'data:' . $mimeType . ';base64,' . $base64Image;

            return $base64ImageUrl;
        } else {
            return null;
        }
    }
}
