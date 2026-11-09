<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaProfissionais;
use App\Models\SgcFaunaCampanhaProfissional;

class ProfissionalService
{
    public function salvarProfissional($contratoId, array $data)
    {
        return SgcFaunaProfissionais::create([
            'id_contrato' => $contratoId,
            'profissional' => $data['profissional'],
            'formacao' => $data['formacao'],
            'telefone' => $data['telefone'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'email' => $data['email'] ?? null,
            'curriculum_lattes' => $data['curriculum_lattes'] ?? null,
            'funcao' => $data['funcao'] ?? null,
            'ctf' => $data['ctf'] ?? null,
            'validade' => $data['validade'] ?? null,
            'conselho_de_classe' => $data['conselho_de_classe'],
            'numero_de_registro' => $data['numero_de_registro'] ?? null,
            'status' => $data['status'],
            'observacao' => $data['observacao'] ?? null,
        ]);
    }

    public function salvarProfissionais($contratoId, $campanhaId, array $profissionais)
    {
        if (!empty($profissionais)) {
            SgcFaunaCampanhaProfissional::where('campanha_id', $campanhaId)->delete();
            foreach ($profissionais as $profissionalData) {
                if (isset($profissionalData['id_profissional']) && $profissionalData['id_profissional']) {
                    SgcFaunaCampanhaProfissional::create([
                        'campanha_id' => $campanhaId,
                        'id_contrato' => $contratoId,
                        'profissional_id' => (int) $profissionalData['id_profissional'],
                        'grupo_faunistico' => $profissionalData['grupo_faunistico'] ?? null,
                        'formacao' => $profissionalData['formacao'] ?? null,
                    ]);
                } else if (isset($profissionalData['profissional'])) {
                    $profissional = SgcFaunaProfissionais::where('id_contrato', $contratoId)
                        ->where('profissional', $profissionalData['profissional'])
                        ->first();
                    if ($profissional) {
                        SgcFaunaCampanhaProfissional::create([
                            'campanha_id' => $campanhaId,
                            'id_contrato' => $contratoId,
                            'profissional_id' => $profissional->id,
                            'grupo_faunistico' => $profissionalData['grupo_faunistico'],
                        ]);
                    }
                }
            }
        }
    }

    public function getProfissionaisByContrato($contratoId)
    {
        return SgcFaunaProfissionais::where('id_contrato', $contratoId)
            ->get(['id', 'profissional', 'formacao'])
            ->toArray();
    }
}