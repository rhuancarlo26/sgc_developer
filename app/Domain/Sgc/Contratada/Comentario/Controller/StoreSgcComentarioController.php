<?php

namespace App\Domain\Sgc\Contratada\Comentario\Controller;

use App\Domain\Sgc\Contratada\Comentario\Services\ComentarioService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreSgcComentarioController extends Controller
{
    public function __construct(private readonly ComentarioService $comentarioService)
    {
    }

    public function index(Request $request)
    {
        $response = $this->comentarioService->salvarComentario($request->all());

        return redirect()->back()->with('success', $response);
    }
}

