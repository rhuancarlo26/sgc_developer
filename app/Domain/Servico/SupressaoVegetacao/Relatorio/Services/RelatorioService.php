<?php

namespace App\Domain\Servico\SupressaoVegetacao\Relatorio\Services;

use App\Models\Servicos;
use App\Models\SupressaoRelatorio;
use App\Models\SupressaoRelatorioAnexo;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class RelatorioService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = SupressaoRelatorio::class;

    public function index(Servicos $servico, array $searchParams): LengthAwarePaginator
    {
        return $this->searchAllColumns(...$searchParams)
            ->with('anexos')
            ->where('fk_servico', $servico->id)
            ->paginate()
            ->appends($searchParams);
    }

    public function store(array $request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'fk_status' => 1,
        ]);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function storeAnexo(array $request): array
    {
        /** @var UploadedFile $arquivo */
        $arquivo = $request['arquivo'];
        $fkRelatorio = $request['fk_relatorio'];
        $nomeArquivo = rand() . '.' . $arquivo->clientExtension();
        $arquivo->storeAs('uploads/supressao/relatorio/anexo', $nomeArquivo);

        SupressaoRelatorioAnexo::query()
            ->create([
                'caminho_arquivo' => 'uploads/supressao/relatorio/anexo/' . $nomeArquivo,
                'nome_arquivo' => $arquivo->getClientOriginalName(),
                'fk_relatorio' => $fkRelatorio,
            ]);

        return [
            'request' => [
                'type'    => 'success',
                'content' => 'Anexo salvo com sucesso!',
            ]
        ];
    }

}
