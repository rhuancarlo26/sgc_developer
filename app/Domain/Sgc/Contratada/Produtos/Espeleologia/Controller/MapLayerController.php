<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Controller;
use App\Shared\Http\Controllers\Controller;

use App\Models\MapLayer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use App\Domain\Sgc\Contratada\Produtos\Services\GeoServerService;

class MapLayerController extends Controller
{
    /**
     * Upload e pré-processamento do shapefile
     * NÃO publica no GeoServer ainda
     */
public function store(Request $request, GeoServerService $geo)
{
    $request->validate([
        'file' => 'required|file|mimes:zip'
    ]);

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

    $idcampanha = $request->input('id_campanha'); // opcional, se quiser vincular à campanha

    // 5️⃣ Cria registro no banco
    $layer = MapLayer::create([
        'name' => $layerName,
        'workspace' => 'jonatas-mapas',
        'datastore' => 'ds_' . time(),
        'layer_name' => $layerName,
        'storage_path' => str_replace(storage_path('app/'), '', $shpPath),
        'id_campanha' => $idcampanha ?? null, // ou vincule à campanha atual se aplicável
    ]);

    try {
        // 6️⃣ GeoServer
        $geo->ensureWorkspace($layer->workspace);

        $geo->createShapefileDatastore(
            $layer->workspace,
            $layer->datastore,
            $shpPath
        );

        $geo->publishLayer(
            $layer->workspace,
            $layer->datastore,
            $layer->layer_name
        );

        $layer->update([
            'published_at' => now()
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Erro ao publicar no GeoServer',
            'details' => $e->getMessage()
        ], 500);
    }

    return response()->json([
        'message' => 'Shapefile publicado com sucesso',
        'layer' => $layer
    ]);
}



    public function publish(MapLayer $layer, GeoServerService $geo)
    {
        // 1️⃣ Garante que o workspace existe
        $geo->ensureWorkspace($layer->workspace);

        // 2️⃣ Caminho ABSOLUTO do shapefile (exigência do GeoServer)
        $absolutePath = storage_path("app/{$layer->storage_path}");

        // 3️⃣ Cria o datastore
        $geo->createShapefileDatastore(
            $layer->workspace,
            $layer->datastore,
            $absolutePath
        );

        // 4️⃣ Publica a layer
        $geo->publishLayer(
            $layer->workspace,
            $layer->datastore,
            $layer->layer_name
        );

        // 5️⃣ Marca como publicada
        $layer->update([
            'published_at' => now()
        ]);

        return response()->json([
            'message' => 'Layer publicada com sucesso no GeoServer.'
        ]);
    }
    public function index()
    {
        return MapLayer::whereNotNull('created_at')
            ->orderBy('created_at')
            ->get([
                'id',
                'layer_name',
                'workspace',
                'layer_name'
            ]);
    }
    // contornando o cors
    public function proxyWms(Request $request)
    {
        // URL base do GeoServer
        $geoserverUrl = 'http://localhost:8080/geoserver/wms';

        // Repassa TODOS os parâmetros recebidos
        $response = Http::withOptions([
            'stream' => true, // importante para imagens grandes
        ])->get($geoserverUrl, $request->query());

        // Retorna exatamente como veio
        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    }

}

