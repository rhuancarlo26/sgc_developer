<?php

namespace App\Domain\Licenca\Controller;

use App\Domain\Licenca\Request\StoreLicencaRequest;
use App\Models\Licenca;
use App\Models\LicencaTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StoreLicencaController extends Controller
{
    public function index(StoreLicencaRequest $request)
    {
        if ($licenca = Licenca::create([
            ...$request->all(),
            'user_id' => Auth::user()->id,
        ])) {
            $tipos = Cache::rememberForever('licenca_tipo', fn () => LicencaTipo::all());
            return to_route('licenca.create', ['licenca' => $licenca, 'tipos' => $tipos])->with('message', [
                'type' => 'success',
                'content' => "Licença criada com sucesso"
            ]);
        }
    }
}
