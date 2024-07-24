<?php

namespace App\Domain\Servico\PMQA\Relatorio\app\Controller;

use App\Domain\Servico\PMQA\Execucao\app\Services\CampanhaService;
use App\Domain\Servico\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\ServicoPmqaRelatorio;
use App\Models\Servicos;
use App\Shared\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PdfController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }

  public function index(Contrato $contrato, Servicos $servico, ServicoPmqaRelatorio $relatorio, Request $request)
  {
    $relatorio->load(
      'status',
      'resultado.analises',
      'resultado.analise_iqa',
      'resultado.outras_analises',
      'resultado.campanhas.pontos.lista.parametros'
    );

    $pontosVinculados = collect($relatorio->resultado->campanhas)->pluck('pontos')->flatten()->unique('id')->values();
    $parametrosVinculados = collect($relatorio->resultado->campanhas)->pluck('pontos')->flatten()->pluck('lista.parametros')->flatten()->unique('id')->values();
    $analiseIqa = $this->showImageAsBase64($relatorio->resultado->analise_iqa->caminho);
    $analises = $relatorio->resultado->analises->mapWithKeys(function ($item) {
      $item['imagem'] = $this->showImageAsBase64($item->caminho);

      return [$item['parametro_id'] => $item];
    });
    $outrasAnalises = $relatorio->resultado->outras_analises->map(function ($item) {
      $item['imagem'] = $this->showImageAsBase64($item->caminho);

      return $item;
    });

    $pdf = Pdf::loadView('Servico.Pmqa.Relatorio.RelatorioPdf', compact('contrato', 'servico', 'relatorio', 'pontosVinculados', 'parametrosVinculados', 'analiseIqa', 'analises', 'outrasAnalises'));

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
