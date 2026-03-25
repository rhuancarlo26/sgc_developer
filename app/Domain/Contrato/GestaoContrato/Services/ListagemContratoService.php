<?php

namespace App\Domain\Contrato\GestaoContrato\Services;

use App\Models\Contrato;
use App\Models\ContratoTipo;
use App\Models\Rodovia;
use App\Models\Servicos;
use App\Models\Uf;
use App\Shared\Abstract\BaseModelService;
use App\Shared\Traits\Deletable;
use App\Shared\Traits\Searchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ListagemContratoService extends BaseModelService
{
    use Searchable, Deletable;

    protected string $modelClass = Contrato::class;

    public function ListagemContratos($tipo, $searchParams): array
    {
        $query = $this->searchAllColumns(...$searchParams)
            ->with(['aditivos'])
            ->where('tipo_contrato', $tipo->id);

        $user = Auth::user();

        if ($user->hasRole('Contratada') || $user->hasRole('Fiscal')) {
            $contratoIds = $user->contratos()->pluck('contratos.id')->toArray();

            Log::info('Usuário logado:', ['id' => $user->id, 'nome' => $user->name]);
            Log::info('Contratos vinculados:', $contratoIds);

            if (empty($contratoIds)) {
                return ['contratos' => collect([])->paginate()];
            }

            $query->whereIn('id', $contratoIds);
        }

        return [
            'contratos' => $query->paginate()->appends($searchParams)
        ];
    }

    public function getServicos($contrato)
    {
        return Servicos::with('tipo')
            ->where('id_contrato', $contrato->id)
            ->where('deleted_at', null)
            ->get();
    }

    public function create($contrato): array
    {
        $ufs      = Cache::rememberForever('ufs', fn() => Uf::all());
        $rodovias = Cache::rememberForever('rodovias', fn() => Rodovia::all());
        $tipos    = Cache::rememberForever('contrato_tipos', fn() => ContratoTipo::all());

        if ($contrato) {
            $contrato->load([
                'tipo',
                'aditivos',
                'trechos',
                'trechos.uf',
                'trechos.rodovia'
            ]);
        }

        return [
            'ufs'      => $ufs,
            'rodovias' => $rodovias,
            'tipos'    => $tipos,
            'contrato' => $contrato
        ];
    }

    public function store($request): array
    {
        return $this->dataManagement->create(entity: $this->modelClass, infos: $request);
    }

    public function update($request): array
    {
        return $this->dataManagement->update(entity: $this->modelClass, infos: $request, id: $request['id']);
    }
}
