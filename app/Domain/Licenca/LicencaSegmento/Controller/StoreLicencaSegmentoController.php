<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreLicencaSegmentoController extends Controller
{
    public function __construct(
        private readonly LicencaSegmentoService $listagemLicencaSegmento,
        private readonly LicencaBRService       $licencaBRService,
        private readonly LicencaTipoService     $licencaTipoService,
        private readonly LicencaService         $licencaService
    ) {
    }

    public function index(StoreLicencaSegmentoRequest $request): RedirectResponse
    {
        $post = [
            ...$request->all(),
            'rodovia_id' => $request->rodovia['id'],
            'uf_inicial_id' => $request->uf_inicial['id'],
            'uf_final_id' => $request->uf_final['id'],
        ];

        $parameters = $this->listagemLicencaSegmento->create(post: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $request->licenca_id])->with('message', $parameters['request']);
    }
}
