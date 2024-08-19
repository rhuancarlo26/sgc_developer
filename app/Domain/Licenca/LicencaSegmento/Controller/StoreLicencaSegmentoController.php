<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreLicencaSegmentoController extends Controller
{
    public function __construct(
        private readonly LicencaSegmentoService $listagemLicencaSegmento
    ) {
    }

    public function index(StoreLicencaSegmentoRequest $request): RedirectResponse
    {
        $post = [
            ...$request->all(),
            'uf_inicial' => $request->uf_inicial['id'],
            'uf_final' => $request->uf_final['id'],
        ];

        $parameters = $this->listagemLicencaSegmento->create(post: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $request->licenca_id])->with('message', $parameters['request']);
    }
}