<?php

namespace App\Domain\Sgc\Contratada\Dav\Services;

use App\Models\Dav;
use App\Models\SgcDavProfissionais;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;

class DavService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Dav::class;
    protected string $davProfissionaisModel = SgcDavProfissionais::class;

    public function salvarDav($dados)
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $dados);

        return $response;
    }

    public function profissionalExiste(string $nome, int $contratoId): bool
    {
        return SgcDavProfissionais::where('contrato_id', $contratoId)
            ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower(trim($nome))])
            ->exists();
    }

    public function salvarDavProfissionais($dados)
    {
        $dados['nome_normalizado'] = mb_strtolower(trim($dados['nome']));

        return $this->dataManagement->create(
            entity: $this->davProfissionaisModel,
            infos: $dados
        );
    }
}
