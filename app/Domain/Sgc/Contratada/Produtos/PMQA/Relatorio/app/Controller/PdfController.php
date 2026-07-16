<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Controller;

use App\Domain\Sgc\Contratada\Produtos\PMQA\Relatorio\app\Services\RelatorioService;
use App\Models\Contrato;
use App\Models\SgcPmqa;
use App\Models\SgcPmqaRelatorio;
use App\Shared\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
  public function __construct(private readonly RelatorioService $relatorioService)
  {
  }

  public function index(Contrato $contrato, string $produto, SgcPmqa $pmqa, SgcPmqaRelatorio $relatorio, Request $request)
  {
    $analiseIqa = null;
    $analises = [];

    $relatorio->load(
      'status',
      'resultado.analises',
      'resultado.analise_iqa',
      'resultado.outras_analises',
      'resultado.campanhas.pontos.lista.parametros',
      'resultado.campanhas.campanha_pontos.ponto',
      'resultado.campanhas.campanha_pontos.medicao.parametros'
    );

    $pontosVinculados = collect($relatorio->resultado->campanhas)->pluck('pontos')->flatten()->unique('id')->values();
    $parametrosVinculados = collect($relatorio->resultado->campanhas)->pluck('pontos')->flatten()->pluck('lista.parametros')->flatten()->unique('id')->values();
    if ($relatorio->resultado->analise_iqa) {
      $analiseIqa = $this->showImageAsBase64(
        $relatorio->resultado->analise_iqa->graf_analise_iqa
      );
    }
    $analises = $relatorio->resultado->analises->mapWithKeys(function ($item) {
      $item['imagem'] = $this->showImageAsBase64(
        $item->graf_analise_parametro
      );

      return [$item['parametro_id'] => $item];
    });
    $outrasAnalises = $relatorio->resultado->outras_analises->map(function ($item) {
      $item['imagem'] = $this->showImageAsBase64(
        $item->caminho_arquivo
      );

      return $item;
    });

    $pdf = Pdf::loadView(
      'Sgc.Contratada.Produtos.Pmqa.Relatorio.RelatorioPdf',
      compact(
        'contrato',
        'produto',
        'pmqa',
        'relatorio',
        'pontosVinculados',
        'parametrosVinculados',
        'analiseIqa',
        'analises',
        'outrasAnalises'
      )
    );

    return $pdf->stream();
  }

  public function showImageAsBase64($caminho)
  {
    if (!$caminho) {
      return null;
    }

    $caminho = str_replace('\\', '/', $caminho);
    $caminho = parse_url($caminho, PHP_URL_PATH) ?: $caminho;
    $caminho = ltrim($caminho, '/');
    $caminho = preg_replace('#^storage/#', '', $caminho);
    $caminho = preg_replace('#^public/#', '', $caminho);

    if (Storage::disk('public')->exists($caminho)) {
      $imageContents = Storage::disk('public')->get($caminho);
      $mimeType = Storage::disk('public')->mimeType($caminho);
      $base64Image = base64_encode($imageContents);
      $base64ImageUrl = 'data:' . $mimeType . ';base64,' . $base64Image;

      return $base64ImageUrl;
    }

    $possiblePaths = [
      storage_path('app/public/' . $caminho),
      storage_path('app/' . $caminho),
      public_path('storage/' . $caminho),
    ];

    foreach ($possiblePaths as $path) {
      if (is_file($path)) {
        $imageContents = file_get_contents($path);
        $mimeType = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mimeType . ';base64,' . base64_encode($imageContents);
      }
    }

    return null;
  }
}
