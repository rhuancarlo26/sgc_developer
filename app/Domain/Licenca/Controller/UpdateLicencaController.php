<?php

namespace App\Domain\Licenca\Controller;

use App\Domain\Licenca\Requests\UpdateLicencaRequest;
use App\Models\Licenca;
use App\Models\LicencaTipo;
use Illuminate\Support\Facades\Cache;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateLicencaController extends Controller
{
    public function index(Licenca $licenca, UpdateLicencaRequest $request)
    {
        $tipos = Cache::rememberForever('licenca_tipo', function () {
            return LicencaTipo::select('id', 'sigla', 'nome')->get();
        });

        if ($licenca->update([
            ...$request->all(),
            'user_id' => Auth::user()->id,
        ])) {
            return to_route('licenca.create', ['tipos' => $tipos, 'licenca' => $licenca->id])->with('message', [
                'type' => 'success',
                'content' => "Licença atualizada com sucesso"
            ]);
        }
    }
}
