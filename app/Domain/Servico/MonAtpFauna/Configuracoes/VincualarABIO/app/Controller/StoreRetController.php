<?php

namespace App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Controller;

use App\Domain\Servico\MonAtpFauna\Configuracoes\VincualarABIO\app\Services\VincularABIOService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreRetController extends Controller
{
  public function __construct(private readonly VincularABIOService $vincularABIOService)
  {
  }

  public function __invoke(Request $request): RedirectResponse
  {
    $this->vincularABIOService->storeRet(request: $request->all());

    return redirect()->back()->with('message', [
        'type' => 'success',
        'content' => 'Arquivo salvo com sucesso!'
    ]);
  }
}
