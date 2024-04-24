<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Requests\StoreLicencaRequest;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class StoreLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(StoreLicencaRequest $request): RedirectResponse
    {
        $post = [
            'tipo_id' => $request->tipo['id'],
            ...$request->all()
        ];

        $response = $this->listagemLicenca->store(request: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $response['model']['id']])
            ->with('message', $response['request']);
    }
}
