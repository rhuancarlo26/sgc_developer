<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaAnexo;
use App\Models\SgcFaunaCampanha;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnexoService
{
    public function salvarAnexos($contratoId, $campanhaId, array $anexos)
    {
        foreach ($anexos as $tipo => $file) {
            if ($file && $file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('anexos', $filename, 'public');
                SgcFaunaAnexo::updateOrCreate(
                    [
                        'id_campanha' => $campanhaId,
                        'id_contrato' => $contratoId,
                        'tipo_anexo' => $tipo,
                    ],
                    [
                        'nome' => $file->getClientOriginalName(),
                        'nome' => $filename,
                        'caminho' => $path,
                        'versao' => 1,
                    ]
                );
                Log::info('Anexo salvo', [
                    'contrato_id' => $contratoId,
                    'campanha_id' => $campanhaId,
                    'tipo_anexo' => $tipo,
                    'caminho' => $path,
                    'nome' => $filename,
                ]);
            }
        }
    }

    public function excluirAnexo($contratoId, $campanhaId, $anexoId)
    {
        $anexo = SgcFaunaAnexo::where('id', $anexoId)
            ->where('id_contrato', $contratoId)
            ->where('id_campanha', $campanhaId)
            ->firstOrFail();

        $campanha = SgcFaunaCampanha::findOrFail($campanhaId);
        if (!in_array($campanha->status, ['Rejeitada', 'Em elaboração'])) {
            throw new \Exception('Apenas campanhas rejeitadas ou em elaboração podem ter anexos excluídos.');
        }

        Storage::disk('public')->delete($anexo->caminho);
        $anexo->delete();
    }
}