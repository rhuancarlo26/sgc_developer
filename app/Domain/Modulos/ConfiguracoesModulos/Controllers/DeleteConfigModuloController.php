<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;

class DeleteConfigModuloController extends Controller
{

    public function delete(Modulo $modulo): RedirectResponse
    {
        $modulo->delete();

        return redirect()->back()->with(key: 'message', value: [
            'type' => 'success',
            'content' => 'Módulo exluído com sucesso!'
        ]);
    }
}
