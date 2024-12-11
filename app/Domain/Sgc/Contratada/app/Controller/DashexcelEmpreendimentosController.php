<?php

namespace App\Domain\Sgc\Contratada\app\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\DashexcelEmpreendimentos;

class DashexcelEmpreendimentosController extends Controller
{
    public function index() {
        $dados = DashexcelEmpreendimentos::all();
        return view('dashexcelempreendimentos.index', compact('dados'));
        // Ou se você preferir retornar como JSON
        // return response()->json($dados);
    }
}
