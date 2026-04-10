<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DeleteConfigModuloController extends Controller
{

    public function delete(Modulo $modulo): RedirectResponse
    {
        if (!is_null($modulo->caminho_planilha_modelo)) {

            $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $modulo->caminho_planilha_modelo;
            if (Storage::exists($caminhoStorage)) {
                Storage::delete($caminhoStorage);
            }
        }

        $modulo->delete();

        return redirect()->back()->with(key: 'message', value: [
            'type' => 'success',
            'content' => 'Módulo exluído com sucesso!'
        ]);
    }
}
