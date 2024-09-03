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

    public function index(StoreLicencaRequest $request): RedirectResponse
    {
        $post = [
            ...$request->all(),
            'tipo' => $request->tipo_rel['id'],
            'status' => $request->status ?? 'Nova',
        ];

        if ($post['tipo'] !== 3) {
            $post['in_app'] = '';
            $post['out_app'] = '';
            $post['total_app'] = '';
            $post['volume'] = '';
            $post['sinaflor'] = '';
        }

        $response = $this->listagemLicenca->store(request: $post);
        
        return to_route(route: 'licenca.create', parameters: ['licenca' => $response['model']['id']])
            ->with('message', $response['request']);
    }
}
