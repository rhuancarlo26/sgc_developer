<?php

namespace App\Domain\Sgc\Contratada\Modulo\ConfigPlanilha\Controllers;

use App\Models\SgcModulo;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Traits\ModulosHandler;
use Inertia\Inertia;
use Inertia\Response;

class CreateConfigModuloController extends Controller
{
    use ModulosHandler;

    public function index($contrato, $produto, SgcModulo $modulo): Response
    {
        return Inertia::render('Sgc/Contratada/Produtos/Modulos/ConfigModulos/Form', [
            'modulo' => $modulo,
            'tipos' => $this->buscarParams(),
            'contrato' => $contrato,
            'produto' => $produto,
        ]);
    }
}
