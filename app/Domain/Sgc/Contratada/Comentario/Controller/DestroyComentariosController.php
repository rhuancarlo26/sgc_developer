<?php

namespace App\Domain\Sgc\Contratada\Comentario\Controller;

use App\Domain\Sgc\Contratada\Comentario\Services\ComentariosService;
use App\Models\SgcComentarios;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DestroyComentariosController extends Controller
{
    public function __construct(private readonly ComentariosService $comentariosService)
    {
    }

    public function index(SgcComentarios $comentarios): RedirectResponse
    {
        try {
            $this->comentariosService->delete($comentarios);

            return redirect()->back()->with('success');
        } catch (\Exception $e) {

            return redirect()->back()->with('error', $e);
        }
    }
}
