<?php

namespace App\Domain\Licenca\app\Services;

use App\Models\Uf;
use Illuminate\Support\Facades\Cache;

class LicencaUFService
{
    public function getLicencaUF(): object
    {
        return Cache::rememberForever('ufs', function () {
            return Uf::select('id', 'estado', 'uf')->get();
        });
    }
}
