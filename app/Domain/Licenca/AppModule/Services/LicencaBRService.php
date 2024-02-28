<?php

namespace App\Domain\Licenca\AppModule\Services;

use App\Models\Rodovia;
use App\Models\Uf;
use Illuminate\Support\Facades\Cache;

class LicencaBRService
{
    public function getLicencaBR(): object
    {
        $brs = Rodovia::select('rodovias.id', 'rodovias.uf_id', 'rodovias.rodovia', 'ufs.estado')
        ->join('ufs', 'rodovias.uf_id', '=', 'ufs.id')
        ->get();
        // dd($brs);
        return $brs;
        // dd($rodoviasSemCache);
        // return Cache::rememberForever('rodovias', function () {
        //     return Rodovia::select('id', 'uf_id', 'rodovia')
        //         ->groupBy('rodovia')
        //         ->get();
        // });

    }

    public function getLicencaUF(String $rodovia): object
    {
        return Rodovia::select('id', 'uf_id')
            ->where('rodovia', '=', intval($rodovia))->get();
    }
}
