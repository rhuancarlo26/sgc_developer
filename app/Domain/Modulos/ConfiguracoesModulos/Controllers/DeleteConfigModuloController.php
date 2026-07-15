<?php

namespace App\Domain\Modulos\ConfiguracoesModulos\Controllers;

use App\Shared\Http\Controllers\Controller;
use App\Models\Modulo;
use App\Models\Servicos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteConfigModuloController extends Controller
{
    public function delete(Modulo $modulo): RedirectResponse
    {
        DB::transaction(function () use ($modulo) {
            Servicos::query()
                ->where('servico_mod_imp_id', $modulo->id)
                ->update([
                    'servico_mod_imp_id' => null,
                    'updated_at' => now(),
                ]);

            if (!is_null($modulo->caminho_planilha_modelo)) {
                $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $modulo->caminho_planilha_modelo;

                if (Storage::exists($caminhoStorage)) {
                    Storage::delete($caminhoStorage);
                }
            }

            $modulo->delete();
        });

        return redirect()->back()->with('message', [
            'type' => 'success',
            'content' => 'Módulo excluído com sucesso!'
        ]);
    }
}
