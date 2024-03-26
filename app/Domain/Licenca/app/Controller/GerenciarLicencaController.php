<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GerenciarLicencaController extends Controller
{
  public function __construct(private readonly LicencaService $listagemLicenca)
  {
  }

  public function index(Request $request): RedirectResponse
  {
    $parameters = $this->listagemLicenca->update(post: $request->all());

    return to_route(route: 'licenca.index', parameters: $parameters['licenca'])
      ->with('message', $parameters['request']);
  }
}
