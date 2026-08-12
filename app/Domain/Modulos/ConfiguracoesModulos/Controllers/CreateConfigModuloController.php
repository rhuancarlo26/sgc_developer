<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Models\Contrato;
use App\Models\Modulo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModulosHandler;
use Inertia\Inertia;
use Inertia\Response;

class CreateConfigModuloController extends Controller
{
    use ModulosHandler;

    public function index(Modulo $modulo): Response
    {
        return Inertia::render('Modulos/ConfigModulos/Form', [
            'modulo' => $modulo,
            'tipos' => $this->buscarParams(),
            'contratos' => Contrato::query()
                ->orderBy('numero_contrato')
                ->get([
                    'id',
                    'numero_contrato',
                    'contratada',
                ]),
        ]);
    }
}
