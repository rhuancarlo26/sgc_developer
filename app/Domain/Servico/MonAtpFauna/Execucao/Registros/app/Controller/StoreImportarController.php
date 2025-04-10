<?php

namespace App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Controller;

use App\Domain\Servico\MonAtpFauna\Execucao\Registros\app\Services\RegistrosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreImportarController extends Controller
{
  public function __construct(private readonly RegistrosService $registrosService) {}

  public function index(Request $request): RedirectResponse
  {
    $response = $this->registrosService->store_importar(post: $request->all());

    return redirect()->back()->with('message', $response['request']);
  }
}
