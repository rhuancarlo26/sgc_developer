<?php

namespace App\Domain\Sgc\Contratada\Coordenadas\Controller;

use App\Domain\Sgc\Contratada\Coordenadas\Services\CoordenadasServices;
use App\Shared\Http\Controllers\Controller;
use App\Models\Rodovia;
use App\Models\UF;
use App\Models\SgcSvnSegGeoV2;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class CoordenadasController extends Controller
{
    public function __construct(private readonly CoordenadasServices $coordenadasServices)
    {
    }
// Retorna o GeoJson com os trechos segmentados segundo a uf, br, km_inicial, km_final
// Controller ajustado
public function getGeoJson(Request $request): JsonResponse
{
    $validated = $request->validate([
        'uf' => 'required|string|size:2',
        'rodovia' => 'required|numeric',
        'km_inicial' => 'required|numeric|min:0',
        'km_final' => 'required|numeric|gte:km_inicial',
        'trecho_tipo' => 'nullable|string',
        'cd_tipo' => 'nullable|numeric',
        'concatenar' => 'nullable|boolean' // Novo parâmetro para controle
    ]);

    try {
        $geojson = SgcSvnSegGeoV2::getGeoJsonFromApi(
            $validated['uf'],
            $validated['rodovia'],
            $validated['km_inicial'],
            $validated['km_final'],
            $validated['trecho_tipo'] ?? null,
            $validated['cd_tipo'] ?? null
        );

        $geojsonTratado = $validated['concatenar'] ?? false 
            ? $this->coordenadasServices->processarGeoJson($geojson)
            : $geojson;

        return response()->json($geojsonTratado);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erro ao processar geojson',
            'message' => $e->getMessage()
        ], 500);
    }
}

// Retorna as latitudes e longetudes apartir da uf, br e kilometro
public function getLatitude(Request $request): JsonResponse
{
    $request->validate([
        'uf_id' => 'required|numeric',
        'rodovia_id' => 'required|numeric',
        'km' => 'required|numeric',
    ]);

    $coordenadas = SgcSvnSegGeoV2::getLatLng(
        UF::find($request->uf_id)->uf,
        Rodovia::find($request->rodovia_id)->br,
        $request->km,
        $request->tipo ?? 'B'
    );

    return response()->json($coordenadas);
}

    // Método para fazer a requisição para a API externa
public function fetchGeoJson(Request $request): JsonResponse
{
    $request->validate([
        'br' => 'required|numeric',
        'tipo' => 'required|string',
        'uf' => 'required|string',
        'data' => 'required|date_format:Y-m-d',
        'kmi' => 'required|numeric',
        'kmf' => 'required|numeric',
    ]);

    // Construindo a URL com os parâmetros recebidos
    $url = "https://servicos.dnit.gov.br/sgplan/apigeo/rotas/espacializarlinha";

    $response = Http::get($url, [
        'br' => $request->br,
        'tipo' => $request->tipo,
        'uf' => $request->uf,
        'cd_tp_trecho' => $request->cd_tp_trecho ?? 'B',
        'data' => $request->data,
        'kmi' => $request->kmi,
        'kmf' => $request->kmf,
    ]);

    // Inspecionando a resposta
    $responseData = $response->json();
    // dd($responseData);
    if ($response->successful() && isset($responseData['type']) && $responseData['type'] === 'Feature') {
        $properties = $responseData['properties'];
        $coordinates = $responseData['geometry']['coordinates'];

        // Processando cada MultiLineString
        foreach ($coordinates as $lineString) {
            foreach ($lineString as $point) {
                $longitude = $point[1];
                $latitude = $point[0];
                // dd([
                //     'uf' => $properties['uf'],
                //     'br' => $properties['br'],
                //     'km_inicial' => $properties['kmi'],
                //     'km_final' => $properties['kmf'],
                //     'latitude' => $latitude,
                //     'longitude' => $longitude,
                //     'tipo_trecho' => $properties['sg_tp_trecho'],
                // ]);
                // Salvando no banco de dados
                SgcSvnSegGeoV2::create([
                    'uf' => $properties['uf'],
                    'br' => $properties['br'],
                    'tipo_trecho' => $properties['sg_tp_trecho'],
                    'km_inicial' => $properties['kmi'],
                    'km_final' => $properties['kmf'],
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            }
        }

        return response()->json(['message' => 'GeoJSON imported successfully']);
    }

    // Em caso de erro ou formato inesperado
    return response()->json(['message' => 'Failed to fetch or parse data from API'], 500);
    }
}



