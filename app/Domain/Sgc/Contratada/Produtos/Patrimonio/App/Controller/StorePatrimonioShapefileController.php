<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Controller;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileProcessorInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts\ShapefileRepositoryInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Request\StorePatrimonioShapefileRequest;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class StorePatrimonioShapefileController extends Controller
{
  public function __construct(
    private ShapefileProcessorInterface $processor,
    private ShapefileRepositoryInterface $repository
  )
  {

  }

  public function __invoke(StorePatrimonioShapefileRequest $request): JsonResponse
  {
    try {
      $geoJson = $this->processor->process($request->file('shapefile'));

      $shapefile = $this->repository->salvar(
        $request->integer('patrimonio_paipa_id'),
        $request->input('nome_campo'),
        $geoJson
      );

      return response()->json(['success' => true, 'data' => $shapefile]);
    } catch (\RuntimeException $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
    }
  }

}
