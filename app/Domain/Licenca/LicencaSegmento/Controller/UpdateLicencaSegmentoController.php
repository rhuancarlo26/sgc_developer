<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Domain\Licenca\LicencaSegmento\Requests\UpdateLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateLicencaSegmentoController extends Controller
{
    public function __construct(
        private readonly LicencaSegmentoService $listagemLicencaSegmento,
        private readonly LicencaBRService       $licencaBRService,
        private readonly LicencaTipoService     $licencaTipoService,
        private readonly LicencaService         $licencaService,
    ) {
    }

    public function index(UpdateLicencaSegmentoRequest $request): \Illuminate\Http\RedirectResponse
    {
        $post = [
            ...$request->validated(),
            'uf_inicial' => $request->uf_inicial['id'],
            'uf_final' => $request->uf_final['id']
        ];
        $parameters = $this->listagemLicencaSegmento->update(post: $post);

        return to_route(
            route: 'licenca.create',
            parameters: [
                // 'tipos'           => $this->licencaTipoService->getLicencaTipo(),
                'licenca'         => $this->licencaService->getLicenca($request->licenca_id),
                // 'licencaSegmento' => $parameters['licencaSegmento'],
                // 'brs'             => $this->licencaBRService->getLicencaBR(),
            ]
        )
            ->with('message', $parameters['request']);
    }
}