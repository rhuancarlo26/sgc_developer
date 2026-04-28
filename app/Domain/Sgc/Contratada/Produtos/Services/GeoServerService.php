<?php

namespace App\Domain\Sgc\Contratada\Produtos\Services;

use Illuminate\Support\Facades\Http;

class GeoServerService
{
    protected string $baseUrl;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        // Ajuste se necessário
        #local
        $this->baseUrl  = 'http://localhost:8080/geoserver/rest';
        $this->user     = 'admin';
        $this->password = 'geoserver';
        #remoto
        $this->baseUrl  = config('services.geoserver.url') . '/rest';
        $this->user     = config('services.geoserver.user');
        $this->password = config('services.geoserver.password');
    }

    /**
     * Método base para chamadas REST ao GeoServer
     */
    protected function request(string $method, string $url, array $body = null)
    {
        $http = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json'
            ]);

        $response = match ($method) {
            'post' => $http->post($url, $body),
            'put'  => $http->put($url, $body),
            'get'  => $http->get($url),
            default => throw new \Exception('Método HTTP não suportado')
        };

        if (! $response->successful()) {
            throw new \Exception($response->body());
        }

        return $response;
    }

    /**
     * Garante que o workspace exista
     */
    public function ensureWorkspace(string $workspace): void
    {
        $url = "{$this->baseUrl}/workspaces/{$workspace}.json";

        // Se existir, não faz nada
        if (Http::withBasicAuth($this->user, $this->password)->get($url)->successful()) {
            return;
        }

        // Caso contrário, cria
        $this->request('post', "{$this->baseUrl}/workspaces", [
            'workspace' => [
                'name' => $workspace
            ]
        ]);
    }

    /**
     * Cria um datastore SHAPEFILE
     * ATENÇÃO: o path precisa apontar para o .shp (não ZIP)
     */
    public function createShapefileDatastore(
        string $workspace,
        string $datastore,
        string $absoluteShpPath
    ): void {
        $url = "{$this->baseUrl}/workspaces/{$workspace}/datastores";

        $payload = [
            'dataStore' => [
                'name' => $datastore,
                'type' => 'Shapefile',
                'connectionParameters' => [
                    'entry' => [
                        [
                            '@key' => 'url',
                            '$' => 'file:' . $absoluteShpPath
                        ],
                        [
                            '@key' => 'charset',
                            '$' => 'UTF-8'
                        ]
                    ]
                ]
            ]
        ];

        $this->request('post', $url, $payload);
    }

    /**
     * Faz upload do ZIP do shapefile diretamente para o GeoServer remoto via HTTP.
     * Substitui createShapefileDatastore + publishLayer quando o GeoServer é remoto,
     * pois o GeoServer não tem acesso ao filesystem local da aplicação.
     * O parâmetro ?configure=all publica automaticamente todos os feature types.
     */
    public function uploadShapefileDatastore(
        string $workspace,
        string $datastore,
        string $absoluteZipPath
    ): void {
        $url = "{$this->baseUrl}/workspaces/{$workspace}/datastores/{$datastore}/file.shp?configure=all&update=overwrite";

        $response = Http::withBasicAuth($this->user, $this->password)
            ->withHeaders(['Content-Type' => 'application/zip'])
            ->withBody(file_get_contents($absoluteZipPath), 'application/zip')
            ->put($url);

        if (! $response->successful()) {
            throw new \Exception($response->body());
        }
    }

    /**
     * Publica a layer a partir do datastore
     * O nome da layer PRECISA ser o nome real do shapefile
     */
    public function publishLayer(
        string $workspace,
        string $datastore,
        string $layerName
    ): void {
        $url = "{$this->baseUrl}/workspaces/{$workspace}/datastores/{$datastore}/featuretypes";

        $payload = [
            'featureType' => [
                'name' => $layerName,
                'nativeName' => $layerName
            ]
        ];

        $this->request('post', $url, $payload);
    }
}
