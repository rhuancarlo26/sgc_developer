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

    public function index(UpdateLicencaRequest $request): \Illuminate\Http\RedirectResponse
    {
        $post = [
            ...$request->all(),
            'tipo' => $request->tipo_rel['id']
        ];

        if ($post['tipo'] !== 3) {
            $post['in_app'] = '';
            $post['out_app'] = '';
            $post['total_app'] = '';
            $post['volume'] = '';
            $post['sinaflor'] = '';
        }

        $response = $this->listagemLicenca->update(post: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $post['id']])
            ->with('message', $response['request']);
    }
}
