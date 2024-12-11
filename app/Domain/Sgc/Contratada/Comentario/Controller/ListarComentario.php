<?php

namespace App\Domain\Sgc\Contratada\Comentario\Controller;

use App\Models\SgcComentario;
use App\Shared\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ListarComentario extends Controller
{
    public function index(): Response
    {
        $comentarios = SgcComentario::all();

        return Inertia::render('Sgc/Contratada/Recurso', [
            'comentarios' => $comentarios
        ]);
    }
}
