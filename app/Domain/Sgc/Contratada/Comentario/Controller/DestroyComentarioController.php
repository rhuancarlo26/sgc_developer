<?php

namespace App\Domain\Sgc\Contratada\Comentario\Controller;

use App\Domain\Sgc\Contratada\Comentario\Services\ComentarioService;
use App\Models\SgcComentario;
use App\Shared\Http\Controllers\Controller;

class DestroyComentarioController extends Controller
{
    public function __construct(private readonly ComentarioService $comentariosService)
    {
    }

    public function destroy(SgcComentario $comentario)
    {
        {
            try {
                $this->comentariosService->delete($comentario);

                return redirect()->back()->with('success');
            } catch (\Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        }
    }
}


