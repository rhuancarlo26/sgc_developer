<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaComentarios;
use App\Models\SgcFaunaAnaliseEtapa;
use Illuminate\Support\Facades\Log;

class ComentarioService
{
    public function salvarComentario($contratoId, $campanhaId, array $data)
    {
        $analise = SgcFaunaAnaliseEtapa::where('id', $data['analise_id'])
            ->where('id_campanha', $campanhaId)
            ->firstOrFail();

        return SgcFaunaComentarios::create([
            'id_contrato' => $contratoId,
            'campanha_id' => $campanhaId,
            'user_id' => auth()->id(),
            'analise' => $analise->analise,
            'etapa' => $data['etapa'],
            'comentario' => $data['comentario'],
            'id_modulo' => $data['id_modulo'] ?? null,
        ]);
    }

    public function excluirComentario($contratoId, $campanhaId, $comentarioId, $userId)
    {
        $comentario = SgcFaunaComentarios::where('id', $comentarioId)
            ->where('id_contrato', $contratoId)
            ->where('campanha_id', $campanhaId)
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $comentario->delete();
        return true;
    }

    public function getComentariosByCampanha($contratoId, $campanhaId)
    {
        return SgcFaunaComentarios::where('id_contrato', $contratoId)
            ->where('campanha_id', $campanhaId)
            ->whereNull('deleted_at')
            ->with(['user' => fn($q) => $q->select('id', 'name')])  // ← ADICIONA ISSO
            ->get()
            ->map(function ($comentario) {
                return [
                    'id' => $comentario->id,
                    'analise' => $comentario->analise,
                    'etapa' => $comentario->etapa,
                    'comentario' => $comentario->comentario,
                    'user' => $comentario->user,  // ← ADICIONA ISSO (traz o user completo)
                    'created_at' => $comentario->created_at,
                ];
            })->toArray();
    }

    
}