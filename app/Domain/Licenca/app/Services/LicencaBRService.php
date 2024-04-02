<?php

namespace App\Domain\Licenca\app\Services;

use App\Models\Rodovia;

class LicencaBRService
{
    public function getLicencaBR(): object
    {
        return Rodovia::select('rodovias.id', 'rodovias.uf_id', 'rodovias.rodovia', 'ufs.estado')
            ->join('ufs', 'rodovias.uf_id', '=', 'ufs.id')
            ->get();
    }

    public function getLicencaUF(string $rodovia): object
    {
        return Rodovia::select('id', 'uf_id')
            ->where('rodovia', '=', intval($rodovia))->get();
    }
}
