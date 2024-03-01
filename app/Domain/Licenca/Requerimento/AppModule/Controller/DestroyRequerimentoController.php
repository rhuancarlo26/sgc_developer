<?php

namespace App\Domain\Licenca\Requerimento\AppModule\Controller;

use App\Domain\Licenca\Requerimento\AppModule\Services\RequerimentoService;
use App\Models\LicencaRequerimento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestroyRequerimentoController extends Controller
{
  public function __construct(private readonly RequerimentoService $requerimentoService)
  {
  }

  public function index(LicencaRequerimento $requerimento): RedirectResponse
  {
    try {
      Storage::delete($requerimento->caminho);

      $requerimento->delete();

      $response = ['type' => 'success', 'content' => 'Requerimento excluido com sucesso!'];
    } catch (\Exception $th) {
      $response = ['type' => 'error', 'content' => $th->getMessage()];
    }


    return to_route('licenca.index')->with('message', $response);
  }
}