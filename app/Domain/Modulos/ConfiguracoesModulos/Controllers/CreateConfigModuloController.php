<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Models\Modulo;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class CreateConfigModuloController extends Controller
{
    public function index(Modulo $modulo): Response
    {
        return Inertia::render('Modulos/ConfigModulos/Form');
    }
}
