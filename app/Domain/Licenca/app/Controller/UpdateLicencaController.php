<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Requests\UpdateLicencaRequest;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;

class UpdateLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(Licenca $licenca, UpdateLicencaRequest $request): \Illuminate\Http\RedirectResponse
    {
        $post = [
            'tipo_id' => $request->tipo['id'],
            ...$request->all()
        ];

        $response = $this->listagemLicenca->update(request: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $licenca->id])
            ->with('message', $response['request']);
    }
}
