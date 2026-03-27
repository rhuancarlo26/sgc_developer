<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;
use App\Shared\Http\Controllers\Controller;

use App\Models\MapLayer;
use App\Models\SgcEspeleoCampanha;
use App\Models\SgcEspeleoCampanhaLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Domain\Sgc\Contratada\Produtos\Services\GeoServerService;

class MapLayerController extends Controller
{
    private function isGeoServerAlreadyExistsError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'already exists')
            || str_contains($message, 'resource named')
            || str_contains($message, 'already configured')
            || str_contains($message, 'name already in use');
    }

    /**
     * Upload e pré-processamento do shapefile
     * NÃO publica no GeoServer ainda
     */
public function store(Request $request, GeoServerService $geo)
{
    $request->validate([
        'file' => 'required|file|mimes:zip',
        'campanha_id' => 'required|exists:sgc_espeleo_campanhas,id',
        'tipo' => 'nullable|string|max:100',
    ]);

    $campanha = SgcEspeleoCampanha::findOrFail($request->input('campanha_id'));

    // 1️⃣ Salva o ZIP
    $zipPath = $request->file('file')->store('shapes');

    // 2️⃣ Pasta de extração
    $extractDir = storage_path('app/shapes/' . pathinfo($zipPath, PATHINFO_FILENAME));
    if (!is_dir($extractDir)) {
        mkdir($extractDir, 0775, true);
    }

    // 3️⃣ Extrai o ZIP
    $zip = new \ZipArchive();
    if ($zip->open(storage_path("app/{$zipPath}")) === true) {
        $zip->extractTo($extractDir);
        $zip->close();
    } else {
        abort(500, 'Falha ao extrair o ZIP');
    }

    // 4️⃣ Localiza o .shp
    $shpFiles = glob($extractDir . '/*.shp');

    if (count($shpFiles) === 0) {
        abort(500, 'Nenhum arquivo .shp encontrado');
    }

    // Usamos o primeiro
    $shpPath = $shpFiles[0];
    $layerName = pathinfo($shpPath, PATHINFO_FILENAME);

    $tipo = $request->input('tipo');
    $workspace = 'ecossistema';
    $isNewLayer = false;

    // 5️⃣ Se já existir no banco (workspace + layer), reaproveita e só vincula campanha.
    // Isso evita erro de unique key (map_layers.uniq_layer).
    $layer = MapLayer::where('workspace', $workspace)
        ->where('layer_name', $layerName)
        ->first();

    if (!$layer) {
        $layer = MapLayer::create([
            'user_id' => auth()->id() ?? 0,
            'workspace' => $workspace,
            'datastore' => 'ds_' . time(),
            'layer_name' => $layerName,
            'title' => $tipo ? str_replace('_', ' ', $tipo) : '',
            'description' => null,
            'geometry_type' => 'Point',
            'crs' => 'EPSG:4674',
            'bbox' => null,
            'storage_path' => str_replace(storage_path('app/'), '', $shpPath),
            'visible' => true,
        ]);
        $isNewLayer = true;
    } elseif (!$layer->published_at) {
        // Se a layer já existe, mantém cadastro atualizado sem criar duplicata.
        $layer->update([
            'title' => $tipo ? str_replace('_', ' ', $tipo) : $layer->title,
            'storage_path' => $layer->storage_path ?: str_replace(storage_path('app/'), '', $shpPath),
            'visible' => true,
        ]);
    }

    SgcEspeleoCampanhaLayer::updateOrCreate(
        [
            'campanha_id' => $campanha->id,
            'map_layer_id' => $layer->id,
        ],
        [
            'tipo' => $tipo,
        ]
    );

    // 6️⃣ Publica no GeoServer apenas quando necessário.
    if ($isNewLayer || !$layer->published_at) {
        try {
            $geo->ensureWorkspace($layer->workspace);

            // Usa upload direto via HTTP para funcionar com GeoServer remoto.
            // O GeoServer remoto não tem acesso ao filesystem local da aplicação.
            $absoluteZipPath = storage_path("app/{$zipPath}");
            $geo->uploadShapefileDatastore(
                $layer->workspace,
                $layer->datastore,
                $absoluteZipPath
            );

            $layer->update([
                'published_at' => now()
            ]);

        } catch (\Throwable $e) {
            if (!$this->isGeoServerAlreadyExistsError($e->getMessage())) {
                return response()->json([
                    'error' => 'Erro ao publicar no GeoServer',
                    'details' => $e->getMessage()
                ], 500);
            }

            $layer->update([
                'published_at' => now()
            ]);
        }
    }

    return response()->json([
        'message' => $isNewLayer ? 'Shapefile publicado com sucesso' : 'Layer existente vinculada com sucesso',
        'layer' => $layer,
        'campanha_id' => $campanha->id,
        'tipo' => $tipo,
    ]);
}



    public function desvincular(Request $request, MapLayer $layer)
    {
        $request->validate([
            'campanha_id' => 'required|exists:sgc_espeleo_campanhas,id',
        ]);

        SgcEspeleoCampanhaLayer::where('campanha_id', $request->input('campanha_id'))
            ->where('map_layer_id', $layer->id)
            ->delete();

        return response()->json(['message' => 'Layer desvinculada da campanha.']);
    }

    public function publish(MapLayer $layer, GeoServerService $geo)
    {
        // 1️⃣ Garante que o workspace existe
        $geo->ensureWorkspace($layer->workspace);

        // 2️⃣ Reconstrói o caminho do ZIP a partir do storage_path do .shp.
        // Estrutura: shapes/{hash}/{Nome}.shp -> zip em shapes/{hash}.zip
        $absoluteShpPath = storage_path("app/{$layer->storage_path}");
        $absoluteZipPath = dirname($absoluteShpPath) . '.zip';

        // 3️⃣ Faz upload do ZIP diretamente para o GeoServer remoto
        $geo->uploadShapefileDatastore(
            $layer->workspace,
            $layer->datastore,
            $absoluteZipPath
        );

        // 4️⃣ Marca como publicada
        $layer->update([
            'published_at' => now()
        ]);

        return response()->json([
            'message' => 'Layer publicada com sucesso no GeoServer.'
        ]);
    }
    public function index(Request $request)
    {
        $campaignId = $request->query('campanha_id');

        $query = MapLayer::query()->whereNotNull('map_layers.created_at');

        if ($campaignId) {
            $query->join('sgc_espeleo_campanha_layers as scl', 'scl.map_layer_id', '=', 'map_layers.id')
                ->where('scl.campanha_id', $campaignId)
                ->select([
                    'map_layers.id',
                    'map_layers.layer_name',
                    'map_layers.workspace',
                    'map_layers.title',
                    'scl.tipo',
                ]);
        } else {
            $query->select([
                'map_layers.id',
                'map_layers.layer_name',
                'map_layers.workspace',
                'map_layers.title',
            ]);
        }

        return $query->orderBy('map_layers.created_at')->get();
    }
    // contornando o cors
    public function proxyWms(Request $request)
    {
        // URL base do GeoServer
        #local
        // $geoserverUrl = 'http://localhost:8080/geoserver/wms';
        #remoto
        $geoserverUrl = 'https://servicos.dnit.gov.br/DPP/geoserver/wms';

        // Repassa TODOS os parâmetros recebidos
        $response = Http::withOptions([
            'stream' => true, // importante para imagens grandes
        ])->get($geoserverUrl, $request->query());

        // Retorna exatamente como veio
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    }

}

