<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\ModuloImportador;
use Exception;

class StatusImportadorService
{
    public function enviarAnalise(ModuloImportador $importador, array $data): void
    {
        $importador->update([
            ...$data,
            'status' => ModuloImportador::ANALISE
        ]);

        $importador->historicos()->create([
            'usuario_id' => auth()->user()->id,
            'status' => ModuloImportador::ANALISE,
            'parecer' => $data['parecer_tecnico']
        ]);
    }

    public function aprovReprov(ModuloImportador $importador, int $status, array $data): void
    {
        $statusDec = match ($status) {
            3 => ModuloImportador::REPROVADO,
            4 => ModuloImportador::APROVADO,
            default => null
        };

        if (is_null($statusDec))
            throw new Exception("Erro tentar reprovar/aprovar a importação - status {$status}");

        $importador->update([
            ...$data,
            'status' => $statusDec
        ]);

        $importador->historicos()->create([
            'usuario_id' => auth()->user()->id,
            'status' => $statusDec,
            'parecer' => $data['parecer_analise']
        ]);
    }
}
