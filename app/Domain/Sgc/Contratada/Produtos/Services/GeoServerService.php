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

    /**
     * Monta um SLD categorizado: uma regra por valor distinto de $propertyName,
     * mais uma regra "else" para valores fora da lista. Inclui os três tipos de
     * symbolizer (polígono/linha/ponto) em cada regra porque o map_layers.geometry_type
     * hoje não é confiável (é gravado como 'Point' fixo independente do shapefile real),
     * então não dá pra decidir de antemão qual symbolizer usar.
     *
     * @param array<int, array{valor: string, cor: string}> $categorias
     */
    public function buildCategorizedSld(
        string $styleName,
        string $propertyName,
        array $categorias,
        string $corPadrao = '#CCCCCC'
    ): string {
        $escape = fn ($value) => htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $symbolizers = function (string $cor) use ($escape) {
            $cor = $escape($cor);

            return '
              <PolygonSymbolizer>
                <Fill><CssParameter name="fill">' . $cor . '</CssParameter><CssParameter name="fill-opacity">0.7</CssParameter></Fill>
                <Stroke><CssParameter name="stroke">#333333</CssParameter><CssParameter name="stroke-width">0.5</CssParameter></Stroke>
              </PolygonSymbolizer>
              <LineSymbolizer>
                <Stroke><CssParameter name="stroke">' . $cor . '</CssParameter><CssParameter name="stroke-width">2.5</CssParameter></Stroke>
              </LineSymbolizer>
              <PointSymbolizer>
                <Graphic>
                  <Mark>
                    <WellKnownName>circle</WellKnownName>
                    <Fill><CssParameter name="fill">' . $cor . '</CssParameter></Fill>
                    <Stroke><CssParameter name="stroke">#333333</CssParameter><CssParameter name="stroke-width">0.5</CssParameter></Stroke>
                  </Mark>
                  <Size>10</Size>
                </Graphic>
              </PointSymbolizer>';
        };

        $property = $escape($propertyName);
        $rules = '';

        foreach ($categorias as $categoria) {
            $valor = $escape($categoria['valor'] ?? '');
            $cor = $categoria['cor'] ?? $corPadrao;

            $rules .= '
            <Rule>
              <Title>' . $valor . '</Title>
              <ogc:Filter>
                <ogc:PropertyIsEqualTo>
                  <ogc:PropertyName>' . $property . '</ogc:PropertyName>
                  <ogc:Literal>' . $valor . '</ogc:Literal>
                </ogc:PropertyIsEqualTo>
              </ogc:Filter>' . $symbolizers($cor) . '
            </Rule>';
        }

        $rules .= '
            <Rule>
              <Title>Outros</Title>
              <ElseFilter/>' . $symbolizers($corPadrao) . '
            </Rule>';

        $styleNameEsc = $escape($styleName);

        return '<?xml version="1.0" encoding="UTF-8"?>
        <StyledLayerDescriptor version="1.0.0"
            xmlns="http://www.opengis.net/sld"
            xmlns:ogc="http://www.opengis.net/ogc"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">
          <NamedLayer>
            <Name>' . $styleNameEsc . '</Name>
            <UserStyle>
              <Title>' . $styleNameEsc . '</Title>
              <FeatureTypeStyle>' . $rules . '
              </FeatureTypeStyle>
            </UserStyle>
          </NamedLayer>
        </StyledLayerDescriptor>';
    }

    /**
     * Cria (POST) ou atualiza (PUT) um style no workspace a partir de um SLD.
     * Usa o corpo cru (não JSON) porque o GeoServer exige Content-Type
     * application/vnd.ogc.sld+xml para este endpoint.
     */
    public function uploadOrUpdateStyle(string $workspace, string $styleName, string $sldXml): void
    {
        $checkUrl = "{$this->baseUrl}/workspaces/{$workspace}/styles/{$styleName}.sld";
        $exists = Http::withBasicAuth($this->user, $this->password)->get($checkUrl)->successful();

        $url = $exists
            ? "{$this->baseUrl}/workspaces/{$workspace}/styles/{$styleName}"
            : "{$this->baseUrl}/workspaces/{$workspace}/styles?name={$styleName}";

        $request = Http::withBasicAuth($this->user, $this->password)
            ->withHeaders(['Content-Type' => 'application/vnd.ogc.sld+xml'])
            ->withBody($sldXml, 'application/vnd.ogc.sld+xml');

        $response = $exists ? $request->put($url) : $request->post($url);

        if (! $response->successful()) {
            throw new \Exception($response->body());
        }
    }

    /**
     * Define o style padrão de uma layer já publicada.
     */
    public function setLayerDefaultStyle(string $workspace, string $layerName, string $styleName): void
    {
        $url = "{$this->baseUrl}/layers/{$workspace}:{$layerName}";

        $this->request('put', $url, [
            'layer' => [
                'defaultStyle' => [
                    'name' => $styleName,
                    'workspace' => $workspace,
                ],
            ],
        ]);
    }
}
