<?php

namespace App\Domain\Licenca\AppModule\Services;

use App\Models\Uf;
use Illuminate\Support\Facades\Cache;

class LicencaUFService
{
    public function getLicencaUF(): object
    {
        // $uf = Uf::select('id', 'estado', 'uf')->get();
        // dd($uf);
        return Cache::rememberForever('ufs', function () {
            return Uf::select('id', 'estado', 'uf')->get();
        });
    }
}
