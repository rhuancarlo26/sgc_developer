<?php

namespace App\Domain\Sgc\Contratada\Comentario\Controller;

use App\Domain\Sgc\Contratada\Comentario\Services\ComentariosService;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreSgcComentariosController extends Controller
{
    public function __construct(private readonly ComentariosService $comentariosService)
    {
    }

    public function index(Request $request)
    {

        $response = $this->comentariosService->salvarComentarios($request->all());

        return redirect()->back()->with('success', $response);
    }
}

