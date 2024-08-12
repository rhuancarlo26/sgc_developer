<?php

namespace App\Domain\Servico\SupressaoVegetacao\Resultado\Services;

use App\Domain\Servico\SupressaoVegetacao\Resultado\Enums\Periodo;
use App\Models\ResultadoSupressao;
use App\Models\Servicos;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\GenerateCode;
use App\Shared\Traits\Searchable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ResultadoService extends BaseModelService
{
    use Searchable, Deletable, GenerateCode;

    protected string $modelClass = ResultadoSupressao::class;

    public function index(Servicos $servico): LengthAwarePaginator
    {
        return $this->model
            ->where('servico_id', $servico->id)
            ->paginate();
    }

    public function store(array $request): array
    {
        if ($request['periodo'] == Periodo::MENSAL->value) {
            $d = $request['mes'] . '-01';
            $request['dt_inicio'] = $d;
            $request['dt_final'] = date("Y-m-t", strtotime($d)) . ' 23:59:59';
        }
        if ($request['periodo'] == Periodo::SEMESTRAL->value) {
            $semestres = [
                '1' => ['-01-01', '-06-30 23:59:59'],
                '2' => ['-07-01', '-12-31 23:59:59']
            ];
            $request['dt_inicio'] = date($request['ano'] . $semestres[$request['semestre']][0]);
            $request['dt_final'] = date($request['ano'] . $semestres[$request['semestre']][1]);
        }
        if ($request['periodo'] == Periodo::ANUAL->value) {
            $request['dt_inicio'] = date($request['ano'] . '-01-01');
            $request['dt_final'] = date($request['ano'] . '-12-31') . ' 23:59:59';
        }
        if ($request['periodo'] == Periodo::PERIODO->value) {
            $request['dt_inicio'] = date($request['dt_inicio']);
            $request['dt_final'] = date($request['dt_final']) . ' 23:59:59';
        }

        return $this->dataManagement->create(entity: $this->modelClass, infos: [
            ...$request,
            'chave' => $this->getCodigo(prefix: 'RS'),
        ]);
    }

    public function update(array $request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }

}
