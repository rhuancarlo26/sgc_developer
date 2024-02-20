<?php

namespace App\Domain\Licenca\Services;

use App\Models\LicencaTipo;
use Illuminate\Support\Facades\Cache;

class LicencaTipoService
{
    public function getLicencaTipo(): object
    {
        return Cache::rememberForever(key: 'licenca_tipo', callback: function () {
            return LicencaTipo::select('id', 'sigla', 'nome')->get();
        });
    }
}
