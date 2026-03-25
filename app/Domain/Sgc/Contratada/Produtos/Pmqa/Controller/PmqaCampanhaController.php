<?php

namespace App\Domain\Sgc\Contratada\Produtos\Pmqa\Controller;

use App\Domain\Sgc\Contratada\Produtos\Pmqa\Requests\ImportarPontosRequest;
use App\Domain\Sgc\Contratada\Produtos\Pmqa\Services\PmqaCampanhaService;
use App\Models\SgcPmqaCampanha;
use App\Models\SgcPmqaListaPivot;
use App\Models\SgcPmqaParametro;
use App\Models\SgcPmqaPonto;
use App\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmqaCampanhaController extends Controller
{
    public function __construct(private readonly PmqaCampanhaService $pmqaCampanhaService) {}

    public function salvarCampanha(Request $request): JsonResponse
    {
        try {
            $campanha = SgcPmqaCampanha::create([
                'id_contrato' => $request->contrato_id,
                'subproduto' => $request->subproduto,
                'fase' => 'criacao',
                'status' => 'Em elaboração',
                'observacoes' => $request->observacoes,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'campanha_id' => $campanha->id,
                'message' => 'Campanha de PMQA criada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao criar campanha'], 500);
        }
    }

    public function importarPontos(ImportarPontosRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            $campanha = SgcPmqaCampanha::findOrFail($validatedData['campanha_id']);
            $arquivo = $validatedData['arquivo'];

            $response = $this->pmqaCampanhaService->importarPontos($campanha, $arquivo);

            return response()->json($response);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Erro na linha {$failure->row()}: {$failure->errors()[0]} (Coluna: {$failure->attribute()})";
            }
            return response()->json(['success' => false, 'message' => 'O arquivo contém erros de validação.', 'errors' => $errors], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ocorreu um erro inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function criarListaParametros(Request $request, $campanhaId): JsonResponse
    {
        try {
            $campanhaId = $request->draftData_id;
            $campanha = SgcPmqaCampanha::findOrFail($campanhaId);
            $parametrosIds = $request->parametros; // Array de IDs de parâmetros, ex.: [3, 5, 7, 8, 9, 10, 12, 20, 21]

            if (empty($parametrosIds)) {
                return response()->json(['success' => false, 'message' => 'Nenhum parâmetro fornecido.'], 400);
            }

            $response = DB::transaction(function () use ($campanha, $parametrosIds, $request) {
                $lista = SgcPmqaParametro::create([
                    'campanha_id' => $campanha->id,
                    'nome' => $request->nome ?? 'Lista de Parâmetros',
                    'chave' => $request->chave ?? null,
                    'medir_iqa' => $request->medir_iqa ?? false,
                ]);

                foreach ($parametrosIds as $parametroId) {
                    DB::table('sgc_pmqa_parametro_pivot')->insert([
                        'lista_id' => $lista->id,
                        'parametro_id' => $parametroId,
                        'observacoes' => $request->observacoes ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $campanha->update(['fase' => 'parametros_criados']);

                return [
                    'lista_id' => $lista->id,
                    'parametro_ids' => $parametrosIds,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Lista e parâmetros associados com sucesso.',
                'lista_id' => $response['lista_id'],
                'parametros_count' => count($response['parametro_ids']),
                'parametro_ids' => $response['parametro_ids'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao criar e vincular parâmetros: ' . $e->getMessage()], 500);
        }
    }

    public function vincularParametrosPontos(Request $request): JsonResponse
    {
        try {
            $campanha = SgcPmqaCampanha::findOrFail($request->campanha_id);

            $vinculos = $request->vinculos;

            DB::transaction(function () use ($campanha, $vinculos) {
                foreach ($vinculos as $pontoId => $parametroIds) {
                    $ponto = SgcPmqaPonto::where('campanha_id', $campanha->id)
                        ->where('id', $pontoId)
                        ->first();

                    if ($ponto) {
                        $ponto->parametros()->sync($parametroIds);
                    }
                }

                $campanha->update(['fase' => 'parametros_vinculados', 'status' => 'Pronto para análise']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Parâmetros vinculados aos pontos com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao vincular parâmetros'], 500);
        }
    }

    public function getPontos($campanhaId): JsonResponse
    {
        $pontos = SgcPmqaPonto::where('campanha_id', $campanhaId)->get();
        return response()->json($pontos);
    }

    public function getParametros($campanhaId): JsonResponse
    {
        $parametros = SgcPmqaParametro::where('campanha_id', $campanhaId)->get();
        return response()->json($parametros);
    }
}
