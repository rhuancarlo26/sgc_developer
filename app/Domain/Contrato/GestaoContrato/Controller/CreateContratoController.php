<?php

namespace App\Domain\Contrato\GestaoContrato\Controller;

use App\Models\ContratoTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CreateContratoController extends Controller
{
    public function create()
    {
        $contratoTipos = Cache::rememberForever('contrato_tipos', fn () => ContratoTipo::all());

        return Inertia::render('Contrato/GestaoContrato/Form', [
            'contratoTipos' => $contratoTipos
        ]);
    }
}