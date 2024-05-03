<?php

namespace App\Domain\Licenca\app\Controller;

use App\Models\Licenca;
use App\Models\LicencaTipo;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class SearchLicencaController extends Controller
{
    public function index($numeroLicenca = null)
    {
        $licenca = null;
        if ($numeroLicenca) {
            $licenca = Licenca::where('numero_licenca', $numeroLicenca)->first();
        }

        $tipos = Cache::rememberForever('licenca_tipo', function () {
            return LicencaTipo::select('id', 'sigla', 'nome')->get();
        });

        return Inertia::render('Licenca/Form', [
            'tipos'     => $tipos,
            'licenca'   => $licenca
        ]);
    }
}
