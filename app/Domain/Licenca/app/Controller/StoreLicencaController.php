<?php

namespace App\Domain\Licenca\app\Controller;

use App\Domain\Licenca\app\Requests\StoreLicencaRequest;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    {
    }

    public function index(Request $request): RedirectResponse
    {
        $post = [
            ...$request->all(),
            'tipo_id' => $request->tipo['id']
        ];

        if (!isset($post['status'])) {
            $post['numero_licenca'] = null;
        }

        if ($post['tipo_id'] !== 3) {
            $post['in_app'] = null;
            $post['out_app'] = null;
            $post['total_app'] = null;
            $post['volume'] = null;
            $post['sinaflor'] = null;
        }

        $response = $this->listagemLicenca->store(request: $post);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $response['model']['id']])
            ->with('message', $response['request']);
    }
}
