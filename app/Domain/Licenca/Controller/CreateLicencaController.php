<?php

namespace App\Domain\Licenca\Controller;

use App\Shared\Http\Controllers\Controller;
use App\Models\Licenca;
use App\Models\LicencaTipo;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CreateLicencaController extends Controller
{
    public function index(Licenca|null $licenca)
    {
        $tipos = Cache::rememberForever('licenca_tipo', function () {
            return LicencaTipo::select('id', 'sigla', 'nome')->get();
        });
        
        return Inertia::render('Licenca/Form', [
            'tipos'     => $tipos,
            'licenca'   => $licenca
        ]);
    }
}
