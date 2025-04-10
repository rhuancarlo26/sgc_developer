<?php

namespace App\Domain\Servico\PassagemFauna\Execucao\Registro\Services;

use App\Models\ServicoPassagemFaunaConfigPassagem;
use App\Models\ServicoPassagemFaunaExecCampanha;
use App\Models\ServicoPassagemFaunaExecRegistro;
use App\Models\ServicoPassagemFaunaExecRegistroImagem;
use App\Models\ServicoPassagemFaunaExecStatusConservacao;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Object_;

class RegistroService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = ServicoPassagemFaunaExecRegistro::class;
    protected string $modelClassImagem = ServicoPassagemFaunaExecRegistroImagem::class;

    public function index($servico, array $searchParams): array
    {
        return [
            'registros' => $this->searchAllColumns(...$searchParams)
                ->with(['passagem', 'imagem', 'status_federal', 'status_iunc'])
                ->where('id_servico', '=', $servico->id)
                ->paginate()
                ->appends($searchParams)
        ];
    }

    public function create($servico)
    {
        return [
            'campanhas' => ServicoPassagemFaunaExecCampanha::where('id_servico', $servico->id)->get(),
            'passagens' => ServicoPassagemFaunaConfigPassagem::where('id_servico', $servico->id)->get(),
            'status_conservacoes' => ServicoPassagemFaunaExecStatusConservacao::all()
        ];
    }

    public function store(array $request)
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function storeArquivo(array $request)
    {
        $nome = $request['arquivo']->getClientOriginalName();
        $caminho = $request['arquivo']->storeAs('public' . DIRECTORY_SEPARATOR . 'Servico' . DIRECTORY_SEPARATOR . 'PassagemFauna' . DIRECTORY_SEPARATOR . 'Execucao' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

        return $this->dataManagement->create(entity: $this->modelClassImagem, infos: [
            'id_registro' => $request['id'],
            'nome' => $nome,
            'caminho_imagem' => str_replace("public\\", "", $caminho)
        ]);
    }

    public function update(array $request)
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

    public function destroy(object $registro)
    {
        $registro->load(['imagem']);

        if ($registro->imagem) {
            Storage::delete($registro->imagem['caminho_imagem']);
        }

        return $this->dataManagement->delete(entity: $this->modelClass, id: $registro->id);
    }

    public function deleteImagem($imagem)
    {
        Storage::delete($imagem->caminho_imagem);

        return $this->dataManagement->delete(entity: $this->modelClassImagem, id: $imagem->id);
    }
}
