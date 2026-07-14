<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\LicencaSegmento\Requests\StoreLicencaSegmentoRequest;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Models\Rodovia;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreLicencaSegmentoController extends Controller
{
    public function __construct(
        private readonly LicencaSegmentoService $listagemLicencaSegmento
    ) {
    }

    public function store(StoreLicencaSegmentoRequest $request): RedirectResponse
    {
        $post = [
            ...$request->all(),
            'uf_inicial' => $request->uf_id,
            'uf_final' => $request->uf_id,
            'km_inicio' => $request->km_inicial,
            'km_fim' => $request->km_final,
            'extensao_br' => $request->extensao,
            'rodovia' => $request->rodovia_id,
            'trecho_tipo' => $request->tipo_trecho,
        ];

        $parameters = $this->listagemLicencaSegmento->create(post: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $request->licenca_id])->with('message', $parameters['request']);
    }
}
