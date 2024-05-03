<?php

namespace App\Domain\Licenca\LicencaSegmento\Controller;

use App\Domain\Licenca\app\Services\LicencaBRService;
use App\Domain\Licenca\app\Services\LicencaService;
use App\Domain\Licenca\app\Services\LicencaTipoService;
use App\Domain\Licenca\LicencaSegmento\Services\LicencaSegmentoService;
use App\Models\LicencaSegmento;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteLicencaSegmentoController extends Controller
{
    public function __construct(
        private readonly LicencaSegmentoService $listagemLicencaSegmento,
        private readonly LicencaBRService       $licencaBRService,
        private readonly LicencaTipoService     $licencaTipoService,
        private readonly LicencaService         $licencaService,
    ) {
    }

    public function index(LicencaSegmento $segmento): RedirectResponse
    {
        $response = $this->listagemLicencaSegmento->delete(post: $segmento);

        return to_route(route: 'licenca.create', parameters: ['licenca' => $segmento->licenca_id])->with('message', $response['request']);
    }
}
