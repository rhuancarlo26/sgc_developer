<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;

class GetUFLicencaSegmentoController extends Controller
{
    public function __construct(private readonly LicencaSegmentoService $listagemUFS)
    { }

    public function index(StoreLicencaSegmentoRequest $request): \Illuminate\Http\RedirectResponse
    {
        $ufs = $this->listagemUFS->getUF($request->rodovia);

        return to_route(
            route: 'licenca.create',
            parameters: [
                'ufs' => $ufs
            ]
        );
    }
}
