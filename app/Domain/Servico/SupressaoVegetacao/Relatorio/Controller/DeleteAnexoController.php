<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Controller;

use App\Models\SupressaoRelatorioAnexo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DeleteAnexoController extends Controller
{

    public function __invoke(SupressaoRelatorioAnexo $anexo): RedirectResponse
    {
        $anexo->delete();

        return redirect()->back()->with(key: 'message', value: [
            'type'    => 'success',
            'message' => 'Anexo deletado com sucesso!',
        ]);
    }

}
