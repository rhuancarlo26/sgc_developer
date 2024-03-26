<?php

namespace App\Domain\Licenca\Controller;

use App\Domain\Licenca\Requests\StoreLicencaRequest;
use App\Domain\Licenca\Services\LicencaService;
use App\Models\Licenca;
use App\Shared\Http\Controllers\Controller;

class StoreLicencaController extends Controller
{
    public function __construct(private readonly LicencaService $listagemLicenca)
    { }

    public function index(StoreLicencaRequest $request): \Illuminate\Http\RedirectResponse
    {   
        
        $post = [
               'user_id' => auth()->user()->id,
            ...$request->all()
        ];

        $response = $this->listagemLicenca->create(post: $post);

        return to_route(route: 'licenca.create', parameters: $response['licenca'])
            ->with('message', $response['request']);
    }
}
