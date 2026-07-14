<?php

namespace App\Domain\Servico\app\Controller;

use App\Models\Servicos;
use App\Models\ServicoRetornoConfeccaoHistorico;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VoltarServicoConfeccaoController extends Controller
{
    public function index(Servicos $servico, Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasAnyRole(['Super Admin', 'Administrador'])) {
            abort(403, 'Você não tem permissão para voltar este serviço para em confecção.');
        }

        if (!in_array((int) $servico->status_aprovacao, [3, 4], true)) {
            throw ValidationException::withMessages([
                'motivo' => 'Só é possível voltar para confecção serviços aprovados ou reprovados.',
            ]);
        }

        $validated = $request->validate([
            'motivo' => ['required', 'string'],
        ], [
            'motivo.required' => 'Informe o motivo para voltar o serviço para em confecção.',
        ]);

        DB::transaction(function () use ($servico, $user, $validated) {
            ServicoRetornoConfeccaoHistorico::create([
                'servico_id' => $servico->id,
                'contrato_id' => $servico->id_contrato,
                'tema_servico' => $servico->tema_servico,
                'servico_mod_imp_id' => $servico->servico_mod_imp_id,
                'user_id' => $user->id,
                'status_anterior' => $servico->status_aprovacao,
                'status_novo' => 1,
                'motivo' => $validated['motivo'],
            ]);

            $servico->update([
                'status_aprovacao' => 1,
            ]);
        });

        return to_route('contratos.contratada.servicos.index', [
            'contrato' => $servico->id_contrato,
        ])->with('message', [
            'type' => 'success',
            'content' => 'Serviço retornado para em confecção com sucesso.',
        ]);
    }
}
