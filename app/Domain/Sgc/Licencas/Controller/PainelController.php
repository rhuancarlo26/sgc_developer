<?php

namespace App\Domain\Sgc\Licencas\Controller;

use App\Domain\Sgc\Licencas\Services\PainelService;
use App\Models\SgcLicenca;
use App\Models\Uf;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PainelController extends Controller
{
    public function __construct(private readonly PainelService $painelService)
    {
    }

    public function index(Request $request): Response
    {
        $filtros = $request->only(['regularizacao', 'uf_id', 'orgao_emissor']);

        return Inertia::render('Sgc/Licencas/Painel', [
            ...$this->painelService->painel($filtros),
            'filtros' => $filtros,
            'ufs' => Uf::orderBy('uf')->get(['id', 'uf']),
            'orgaos' => SgcLicenca::ativas()->distinct()->orderBy('orgao_emissor')->pluck('orgao_emissor'),
        ]);
    }

    public function regularizacao(Request $request): Response
    {
        $ano = (int) $request->integer('ano', now()->year);

        return Inertia::render('Sgc/Licencas/Regularizacao', $this->painelService->regularizacao($ano));
    }
}
