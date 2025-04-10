<?php

namespace App\Domain\Sgc\Contratada\Dav\Controller;

use App\Domain\Sgc\Contratada\Dav\Services\DavConsumoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DavConsumoController extends Controller
{
  protected $davConsumoService;

  public function __construct(DavConsumoService $davConsumoService)
  {
    $this->davConsumoService = $davConsumoService;
  }

  public function update(Request $request)
  {
    try {
      $dados = $request->validate([
        'contrato' => 'required|string', // Ajustado para string, já que "93/2022" é string
        'diarias' => 'nullable|integer',
        'hatch' => 'nullable|integer',
        'pickup' => 'nullable|integer',
        'barco' => 'nullable|integer',
        'aerea' => 'nullable|integer',
      ]);

      $registroAtualizado = $this->davConsumoService->updateConsumo($dados);

      return response()->json([
        'success' => true,
        'message' => 'Registro atualizado com sucesso!',
        'data' => $registroAtualizado,
      ], 200);

    } catch (\InvalidArgumentException $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 400);

    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}
