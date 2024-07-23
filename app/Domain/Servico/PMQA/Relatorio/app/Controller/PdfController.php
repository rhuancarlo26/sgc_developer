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
    $campanhaPontos = collect($relatorio->resultado->campanhas)->pluck('pontos')->flatten()->pluck('nomepontocoleta')->unique()->values();

    $pdf = Pdf::loadView('Servico.Pmqa.Relatorio.RelatorioPdf', compact('contrato', 'servico', 'relatorio', 'pontosVinculados', 'parametrosVinculados', 'campanhaPontos'));

    return $pdf->stream();
  }
}
