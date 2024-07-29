<?php

namespace App\Domain\Licenca\Shapefile\Controller;

use App\Domain\Licenca\Shapefile\Requests\StoreLicencaShapefileRequest;
use App\Domain\Licenca\Shapefile\Services\LicencaShapefileService;
use App\Shared\Http\Controllers\Controller;

class StoreLicencaShapefileController extends Controller
{
    public function __construct(private readonly LicencaShapefileService $licencaShapefileService)
    {
    }

    public function index(StoreLicencaShapefileRequest $request)
    {
        dd($request);
        $response = $this->licencaShapefileService->store($request->validated());

        return to_route('licenca.create', ['licenca' => $request->licenca_id])->with('message', $response['request']);
    }
}
