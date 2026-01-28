<?php

namespace App\Domain\Sgc\Contratada\Produtos\PMQA\Execucao\Medir\app\Services;

use App\Models\ServicoPmqaCampanhaPontoMedicao;
use App\Models\ServicoPmqaCampanhaPontoMedicaoArquivo;
use App\Models\ServicoPmqaCampanhaPontoMedicaoParametro;
use App\Models\SgcPmqaCampanhaPontoMedicao;
use App\Models\SgcPmqaCampanhaPontoMedicaoArquivo;
use App\Models\SgcPmqaCampanhaPontoMedicaoParametro;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Storage;

class MedicaoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = SgcPmqaCampanhaPontoMedicao::class;
    protected string $modelClassArquivo = SgcPmqaCampanhaPontoMedicaoArquivo::class;

    public function store(array $request): array
    {
        $response = $this->dataManagement->create(entity: $this->modelClass, infos: $request);

        if ($request['parametros']) {
            $this->syncParametros($response['model'], $request['parametros']);
        }

        return $response;
    }

    public function update(array $request): array
    {
        $response = $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);

        if ($request['parametros']) {
            $this->syncParametros(SgcPmqaCampanhaPontoMedicao::find($request['id']), $request['parametros']);
        }

        return $response;
    }

    public function syncParametros(SgcPmqaCampanhaPontoMedicao $medicao, array $parametros): void
    {
        SgcPmqaCampanhaPontoMedicaoParametro::where('pmqa_ponto_medicao_id', $medicao->id)->delete();

        foreach ($parametros as $key => $value) {
            SgcPmqaCampanhaPontoMedicaoParametro::create([
                'pmqa_ponto_medicao_id' => $medicao->id,
                'parametro_id' => $key,
                'medicao' => $value
            ]);
        }
    }

    public function storeArquivo(array $request)
    {
        if ($request['arquivo']->isvalid()) {
            $nome = $request['arquivo']->getClientOriginalName();
            $caminho = $request['arquivo']->storeAs('Servico' . DIRECTORY_SEPARATOR . 'Pmqa' . DIRECTORY_SEPARATOR . 'Medicao' . DIRECTORY_SEPARATOR . uniqid() . '_' . $nome);

            return $this->dataManagement->create(entity: $this->modelClassArquivo, infos: [
                'fk_ponto_medicao' => $request['id'],
                'nome' => $nome,
                'caminho_arquivo' => $caminho
            ]);
        }
    }

    public function deleteArquivo(SgcPmqaCampanhaPontoMedicaoArquivo $arquivo): array
    {
        Storage::delete($arquivo->caminho_arquivo);

        return $this->dataManagement->delete(entity: $this->modelClassArquivo, id: $arquivo->id);
    }
}
