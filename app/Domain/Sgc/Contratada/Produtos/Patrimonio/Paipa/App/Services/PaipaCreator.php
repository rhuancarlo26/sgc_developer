<?php

namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\Paipa\App\Services;

use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoCreatorInterface;
use App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Domain\Contracts\SubprodutoInterface;
use App\Models\SgcPatrimonioEquipe;
use App\Models\SgcPatrimonioPaipa;

class PaipaCreator implements SubprodutoCreatorInterface
{
    public function create(array $data): SubprodutoInterface
    {
        // Criar uma classe wrapper ou fazer o SgcPatrimonioPaipa implementar a interface
        $paipa = isset($data['paipa_id'])
          ? SgcPatrimonioPaipa::findOrFail($data['paipa_id'])
          : new SgcPatrimonioPaipa();
        $paipa->contrato_id = $data['contrato_id'] ?? null;
        $paipa->empreendimento_id = $data['empreendimento_id'] ?? $data['empreendimento'] ?? null;
        $paipa->justificativa_sei = $data['justificativa_sei'] ?? null;
        $paipa->justificativa_titulo = $data['justificativa_titulo'] ?? null;
        $paipa->justificativa_citacao = $data['justificativa_citacao'] ?? null;
        $paipa->justificativa_complementar = $data['justificativa_complementar'] ?? null;
        $paipa->status = $paipa->status ?? 'Em elaboração';
        $paipa->versao = $paipa->versao ?? 1;

        $paipa->save();

        $syncEquipe = [];

        foreach ($data['equipe'] ?? [] as $membro) {
          $equipe = !empty($membro['id'])
            ? SgcPatrimonioEquipe::findOrFail($membro['id'])
            : SgcPatrimonioEquipe::create([
              'nome' => $membro['nome'],
              'cpf' => $membro['cpf'] ?? null,
              'cnpj' => $membro['cnpj'] ?? null,
              'email' => $membro['email'] ?? null,
              'profissao' => $membro['profissao'] ?? null,
              'funcao' => $membro['funcao'] ?? null,
              'conselho_classe' => $membro['conselho_classe'] ?? null,
              'numero_registro' => $membro['numero_registro'] ?? null,
              'carteira_profissional' => $membro['carteira_profissional'] ?? null,
              'ct' => $membro['ct'] ?? null,
              'obs' => $membro['obs'] ?? null,
              'ativo' => true,
            ]);

          $syncEquipe[$equipe->id] = [
            'tipo_participacao' => $membro['tipo_participacao'] ?? null,
            'data_inicio' => now(),
            'ativo' => true,
          ];
        }

        if (!empty($syncEquipe)) {
          $paipa->equipe()->sync($syncEquipe);
        }

        return new PaipaAdapter($paipa);
    }

    public function supports(string $type): bool
    {
        return $type === 'paipa';
    }
}
