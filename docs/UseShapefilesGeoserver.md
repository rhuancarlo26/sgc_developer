# Guia: Upload e Visualização de Shapefiles com GeoServer

Padrão estabelecido no módulo **Espeleologia** (`app/Domain/Sgc/Contratada/Produtos/Espeleologia/`).  
Use este guia para replicar a mesma funcionalidade em outros módulos.

---

## Visão Geral do Fluxo

```
[Usuário] → ZIP contendo .shp
    │
    ├─► [Frontend] Extrai .shp do ZIP (JSZip + shapefile.js)
    ├─► [Frontend] Renderiza preview local (Leaflet + GeoJSON)
    ├─► [Frontend] Envia FormData para o backend (axios.post)
    │
    ├─► [Backend]  Salva ZIP em storage/app/shapes/
    ├─► [Backend]  Extrai arquivos e localiza .shp
    ├─► [Backend]  Cria registro em `map_layers`
    ├─► [Backend]  Vincula à campanha em `{modulo}_campanha_layers`
    ├─► [Backend]  Publica no GeoServer via REST (GeoServerService)
    │
    └─► [Frontend] MapViewer carrega camadas via WMS (proxy Laravel)
```

---

## 1. Banco de Dados

### 1.1 Tabela Compartilhada: `map_layers`

Já existe no sistema. **Não crie outra.** Todos os módulos compartilham esta tabela.

```
map_layers
├── id
├── user_id
├── workspace        → 'ecossistema' (padrão)
├── datastore        → 'ds_{timestamp}'
├── layer_name       → nome do arquivo .shp (único por workspace)
├── title            → nome amigável
├── geometry_type    → Point | LineString | Polygon
├── crs              → 'EPSG:4674'
├── storage_path     → caminho relativo em storage/app/
├── published_at     → timestamp da publicação no GeoServer
└── visible
```

### 1.2 Tabela Pivot do Módulo: `{modulo}_campanha_layers`

Cada módulo cria sua própria tabela pivot para vincular campanhas às layers.  
Crie uma migration específica:

```php
// database/migrations/YYYY_MM_DD_create_{modulo}_campanha_layers_table.php

Schema::create('{modulo}_campanha_layers', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('campanha_id');
    $table->unsignedBigInteger('map_layer_id');
    $table->string('tipo')->nullable();   // ex: 'hidrologico', 'vegetacao', etc.
    $table->timestamps();

    $table->unique(['campanha_id', 'map_layer_id']);
    $table->foreign('campanha_id')->references('id')->on('{modulo}_campanhas')->onDelete('cascade');
    $table->foreign('map_layer_id')->references('id')->on('map_layers')->onDelete('cascade');
});
```

### 1.3 Model Pivot

```php
// app/Domain/{Modulo}/Models/{Modulo}CampanhaLayer.php

namespace App\Domain\{Modulo}\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Models\MapLayer;

class {Modulo}CampanhaLayer extends Model
{
    protected $table = '{modulo}_campanha_layers';

    protected $fillable = ['campanha_id', 'map_layer_id', 'tipo'];

    public function campanha()
    {
        return $this->belongsTo({Modulo}Campanha::class, 'campanha_id');
    }

    public function layer()
    {
        return $this->belongsTo(MapLayer::class, 'map_layer_id');
    }
}
```

### 1.4 Relacionamento no Model da Campanha

Adicione ao model principal da campanha do módulo:

```php
// Em {Modulo}Campanha.php

use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Models\MapLayer;

public function mapLayers()
{
    return $this->belongsToMany(
        MapLayer::class,
        '{modulo}_campanha_layers',
        'campanha_id',
        'map_layer_id'
    )->withPivot('tipo')->withTimestamps();
}
```

---

## 2. Backend

### 2.1 GeoServerService

Já existe em `app/Domain/Sgc/Contratada/Produtos/Services/GeoServerService.php`.  
**Não duplique.** Injete via construtor ou type-hint nos controllers.

```php
use App\Domain\Sgc\Contratada\Produtos\Services\GeoServerService;

// Injeção automática pelo container do Laravel:
public function store(Request $request, GeoServerService $geo) { ... }
```

Métodos disponíveis:
- `ensureWorkspace(string $workspace)` — cria workspace se não existir
- `uploadShapefileDatastore(string $workspace, string $datastore, string $absoluteZipPath)` — faz PUT do ZIP no GeoServer

### 2.2 Controller de Layers

Crie um controller específico do módulo. Copie a lógica do `MapLayerController` e adapte:

```php
// app/Domain/{Modulo}/Controller/{Modulo}MapLayerController.php

namespace App\Domain\{Modulo}\Controller;

use App\Domain\Sgc\Contratada\Produtos\Services\GeoServerService;
use App\Domain\{Modulo}\Models\{Modulo}Campanha;
use App\Domain\{Modulo}\Models\{Modulo}CampanhaLayer;
use App\Shared\Http\Controllers\Controller;
use App\Domain\Sgc\Contratada\Produtos\Espeleologia\Models\MapLayer;
use Illuminate\Http\Request;
use ZipArchive;

class {Modulo}MapLayerController extends Controller
{
    // Lista layers de uma campanha
    public function index(Request $request)
    {
        $campanhaId = $request->query('campanha_id');

        $layers = {Modulo}CampanhaLayer::where('campanha_id', $campanhaId)
            ->with('layer')
            ->get()
            ->map(fn($cl) => [
                'id'         => $cl->layer->id,
                'layer_name' => $cl->layer->layer_name,
                'workspace'  => $cl->layer->workspace,
                'title'      => $cl->layer->title,
                'tipo'        => $cl->tipo,
            ]);

        return response()->json($layers);
    }

    // Upload e publicação de shapefile
    public function store(Request $request, GeoServerService $geo)
    {
        $request->validate([
            'file'        => 'required|file|mimes:zip',
            'campanha_id' => 'required|exists:{modulo}_campanhas,id',
            'tipo'        => 'nullable|string|max:100',
        ]);

        $campanha  = {Modulo}Campanha::findOrFail($request->input('campanha_id'));
        $tipo      = $request->input('tipo');
        $workspace = config('geoserver.workspace', 'ecossistema');

        // 1. Armazena ZIP
        $zipPath   = $request->file('file')->store('shapes');
        $extractDir = storage_path('app/shapes/' . pathinfo($zipPath, PATHINFO_FILENAME));

        // 2. Extrai
        $zip = new ZipArchive();
        if ($zip->open(storage_path("app/{$zipPath}")) === true) {
            $zip->extractTo($extractDir);
            $zip->close();
        }

        // 3. Localiza .shp
        $shpFiles = glob($extractDir . '/*.shp');
        if (empty($shpFiles)) {
            abort(422, 'Nenhum arquivo .shp encontrado no ZIP.');
        }

        $layerName = pathinfo($shpFiles[0], PATHINFO_FILENAME);
        $isNewLayer = false;

        // 4. Cria ou reutiliza MapLayer
        $layer = MapLayer::where('workspace', $workspace)
            ->where('layer_name', $layerName)
            ->first();

        if (! $layer) {
            $layer = MapLayer::create([
                'user_id'      => auth()->id(),
                'workspace'    => $workspace,
                'datastore'    => 'ds_' . time(),
                'layer_name'   => $layerName,
                'title'        => $tipo ? str_replace('_', ' ', $tipo) : $layerName,
                'geometry_type' => 'Point',
                'crs'          => 'EPSG:4674',
                'storage_path' => str_replace(storage_path('app/'), '', $shpFiles[0]),
                'visible'      => true,
            ]);
            $isNewLayer = true;
        }

        // 5. Vincula à campanha
        {Modulo}CampanhaLayer::updateOrCreate(
            ['campanha_id' => $campanha->id, 'map_layer_id' => $layer->id],
            ['tipo' => $tipo]
        );

        // 6. Publica no GeoServer (apenas se ainda não publicado)
        if ($isNewLayer || ! $layer->published_at) {
            try {
                $geo->ensureWorkspace($layer->workspace);
                $geo->uploadShapefileDatastore(
                    $layer->workspace,
                    $layer->datastore,
                    storage_path("app/{$zipPath}")
                );
                $layer->update(['published_at' => now()]);
            } catch (\Throwable $e) {
                // GeoServer retorna 500 se layer já existe — trata como sucesso
                if (! str_contains($e->getMessage(), 'already exists')) {
                    throw $e;
                }
                $layer->update(['published_at' => now()]);
            }
        }

        return response()->json([
            'message'     => $isNewLayer ? 'Shapefile publicado com sucesso.' : 'Layer existente vinculada.',
            'layer'       => $layer,
            'campanha_id' => $campanha->id,
            'tipo'        => $tipo,
        ]);
    }

    // Desvincular layer da campanha (não apaga do GeoServer)
    public function desvincular(Request $request, int $layerId)
    {
        $campanhaId = $request->query('campanha_id');

        {Modulo}CampanhaLayer::where('campanha_id', $campanhaId)
            ->where('map_layer_id', $layerId)
            ->delete();

        return response()->json(['message' => 'Layer desvinculada da campanha.']);
    }
}
```

### 2.3 Rotas

No arquivo de rotas do módulo (ou em `routes/web.php`):

```php
// routes/web.php (dentro do grupo de middleware do módulo)

Route::prefix('{modulo}')
    ->name('{modulo}.')
    ->group(function () {

        // ... rotas existentes do módulo ...

        // Layers / Shapefiles
        Route::get('layers',                    [{Modulo}MapLayerController::class, 'index'])
            ->name('layers.index');
        Route::post('layers/upload-shapefile',  [{Modulo}MapLayerController::class, 'store'])
            ->name('layers.upload_shapefile');
        Route::delete('layers/{layer}/desvincular', [{Modulo}MapLayerController::class, 'desvincular'])
            ->name('layers.desvincular');
    });
```

> **Rota WMS já existe** em `/sgc/contratada/mapa/wms` (MapPageController::proxyWms). Todos os módulos reusam essa mesma rota proxy.

---

## 3. Frontend

### 3.1 Componente de Upload: `UploadShapeGeoserver.vue`

Crie em `resources/js/Components/Mapa/UploadShapeGeoserver.vue` — ou copie e adapte de  
`resources/js/Pages/Sgc/Contratada/Produtos/Espeleologia/ResultadosGeoserver.vue`.

O componente recebe como props:
- `campanhaId` — ID da campanha
- `tiposMapa` — array de tipos disponíveis para aquele módulo
- `uploadRoute` — nome da rota (ex: `'sgc.contratada.{modulo}.layers.upload_shapefile'`)
- `layersRoute` — nome da rota de listagem

**Estrutura mínima do componente:**

```vue
<script setup>
import { ref, nextTick } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import JSZip from 'jszip'
import * as shapefile from 'shapefile'

const props = defineProps({
  campanhaId:  { type: [Number, String], required: true },
  tiposMapa:   { type: Array, required: true },   // [{ value: 'geologico', label: 'Mapa Geológico' }]
  uploadRoute: { type: String, required: true },
  layersRoute: { type: String, required: true },
})

const emit = defineEmits(['layer-added'])

// Estado por tipo de mapa
const zipFiles  = ref({})   // { tipo: File }
const features  = ref({})   // { tipo: GeoJSON[] }
const rendered  = ref({})   // { tipo: boolean }
const maps      = ref({})   // { tipo: LeafletMap }
const isLoading = ref(false)

// 1. Extração local do ZIP
async function processFiles(files, tipo) {
  const zipFile = Array.from(files).find(f => f.name.endsWith('.zip'))
  if (! zipFile) return

  zipFiles.value[tipo] = zipFile
  features.value[tipo] = []
  rendered.value[tipo] = false

  try {
    isLoading.value = true
    const zip = await JSZip.loadAsync(zipFile)
    const shpName = Object.keys(zip.files).find(n => n.endsWith('.shp'))
    if (! shpName) throw new Error('ZIP não contém arquivo .shp')

    const shpBuffer = await zip.file(shpName).async('arraybuffer')
    const geojson   = await shapefile.read(shpBuffer, null, { name: 'features' })
    features.value[tipo] = geojson.features ?? []

    rendered.value[tipo] = true
    await nextTick()
    renderPreview(tipo)
  } finally {
    isLoading.value = false
  }
}

// 2. Preview local com Leaflet
function renderPreview(tipo) {
  const container = document.getElementById(`map-preview-${tipo}`)
  if (! container) return

  if (maps.value[tipo]) {
    maps.value[tipo].remove()
  }

  const map = L.map(container).setView([-14.235, -51.925], 5)
  maps.value[tipo] = map

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
  }).addTo(map)

  if (features.value[tipo]?.length) {
    const layer = L.geoJSON(features.value[tipo]).addTo(map)
    map.fitBounds(layer.getBounds().pad(0.1))
  }

  setTimeout(() => map.invalidateSize(), 300)
}

// 3. Envio para o backend / GeoServer
async function vincularMapa(tipo) {
  const zipFile = zipFiles.value[tipo]
  if (! zipFile) return

  const formData = new FormData()
  formData.append('file', zipFile)
  formData.append('campanha_id', props.campanhaId)
  formData.append('tipo', tipo)

  try {
    isLoading.value = true
    const { data } = await axios.post(
      route(props.uploadRoute),
      formData,
      { headers: { 'Content-Type': 'multipart/form-data' } }
    )

    if (data.layer) {
      emit('layer-added', { ...data.layer, tipo })
      // Limpa estado deste tipo
      zipFiles.value[tipo]  = null
      features.value[tipo]  = []
      rendered.value[tipo]  = false
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div v-for="tipo in tiposMapa" :key="tipo.value" class="mb-4">
    <h6>{{ tipo.label }}</h6>

    <!-- Drop Zone -->
    <div
      class="border rounded p-3 text-center bg-light"
      @dragover.prevent
      @drop.prevent="processFiles($event.dataTransfer.files, tipo.value)"
    >
      <p class="mb-1">Arraste o arquivo <strong>.zip</strong> aqui ou</p>
      <label class="btn btn-sm btn-outline-secondary">
        Selecionar arquivo
        <input type="file" class="d-none" accept=".zip"
          @change="processFiles($event.target.files, tipo.value)" />
      </label>
    </div>

    <!-- Preview local -->
    <div v-if="rendered[tipo.value]" :id="`map-preview-${tipo.value}`"
         style="height: 300px; margin-top: 8px; border-radius: 4px;"></div>

    <!-- Botão de envio -->
    <button
      v-if="features[tipo.value]?.length"
      class="btn btn-primary btn-sm mt-2"
      :disabled="isLoading"
      @click="vincularMapa(tipo.value)"
    >
      {{ isLoading ? 'Publicando...' : 'Vincular ao GeoServer' }}
    </button>
  </div>
</template>
```

### 3.2 Componente de Visualização: `MapViewer.vue`

Já existe em `resources/js/Pages/Sgc/Contratada/Produtos/Espeleologia/MapViewer.vue`.  
Ele é **genérico o suficiente para ser reutilizado diretamente**.

Props que ele aceita:
```js
defineProps({
  campanhaId:        [Number, String],   // ID da campanha
  layersRoute:       String,             // nome da rota GET de layers
  emp_coordenadas:   [Object, String],   // GeoJSON do empreendimento (opcional)
})
```

Considere mover `MapViewer.vue` para `resources/js/Components/Mapa/MapViewer.vue` e importar nos módulos, evitando duplicação.

### 3.3 Definindo os Tipos de Mapa por Módulo

Cada módulo declara seus próprios tipos. Exemplos:

```js
// Espeleologia (referência)
const tiposMapaEspeleologia = [
  { value: 'geologico',            label: 'Mapa Geológico' },
  { value: 'geomorfologico',       label: 'Mapa Geomorfológico' },
  { value: 'hipsometrico',         label: 'Mapa Hipsométrico' },
  { value: 'declividades',         label: 'Mapa de Declividades' },
  { value: 'hidrografico',         label: 'Mapa Hidrográfico' },
  { value: 'cavidades',            label: 'Cavidades CECAV/SBE' },
  { value: 'potencial_inicial',    label: 'Potencial Espeleológico - Inicial' },
  { value: 'potencial_reclassificado', label: 'Potencial Espeleológico - Reclassificado' },
  { value: 'projeto_engenharia',   label: 'Projeto de Engenharia' },
]

// Exemplo para Passagem de Fauna
const tiposMapaPassagemFauna = [
  { value: 'localizacao_passagem', label: 'Localização das Passagens' },
  { value: 'uso_fauna',            label: 'Uso por Fauna' },
  { value: 'areas_influencia',     label: 'Áreas de Influência' },
]

// Exemplo para Supressão de Vegetação
const tiposMapaSupressao = [
  { value: 'area_supressao',       label: 'Área de Supressão' },
  { value: 'fitofisionomia',       label: 'Fitofisionomia' },
  { value: 'cronograma_espacial',  label: 'Cronograma Espacial' },
]
```

### 3.4 Usando os Componentes na Página do Módulo

```vue
<script setup>
import UploadShapeGeoserver from '@/Components/Mapa/UploadShapeGeoserver.vue'
import MapViewer from '@/Components/Mapa/MapViewer.vue'
import { ref } from 'vue'

const props = defineProps({
  campanha: Object,
  campanhaId: [Number, String],
})

const geoLayers = ref(props.campanha?.mapLayers ?? [])

function onLayerAdded(layer) {
  geoLayers.value.push(layer)
}

const tiposMapa = [
  { value: 'area_supressao', label: 'Área de Supressão' },
  // ...
]
</script>

<template>
  <!-- Upload -->
  <UploadShapeGeoserver
    :campanha-id="campanhaId"
    :tipos-mapa="tiposMapa"
    upload-route="sgc.contratada.{modulo}.layers.upload_shapefile"
    layers-route="sgc.contratada.{modulo}.layers.index"
    @layer-added="onLayerAdded"
  />

  <!-- Visualização -->
  <MapViewer
    :campanha-id="campanhaId"
    layers-route="sgc.contratada.{modulo}.layers.index"
    :emp_coordenadas="campanha.empreendimento?.coordenadas"
  />
</template>
```

---

## 4. Passo a Passo para Novos Módulos

### Checklist de Implementação

```
[ ] 1. Migration: criar tabela {modulo}_campanha_layers
        (copiar de Espeleologia, ajustar foreign key)

[ ] 2. Model: criar {Modulo}CampanhaLayer.php
        (ajustar $table e relacionamentos)

[ ] 3. Model: adicionar relacionamento mapLayers() ao model da campanha

[ ] 4. Controller: criar {Modulo}MapLayerController.php
        (copiar lógica de MapLayerController, ajustar validação e model)

[ ] 5. Rotas: adicionar 3 rotas (index, upload-shapefile, desvincular)

[ ] 6. Definir array tiposMapa específico do módulo

[ ] 7. Usar UploadShapeGeoserver.vue + MapViewer.vue nas páginas Vue
        (ou criar página de ResultadosGeoserver específica se necessário)

[ ] 8. Passar geoLayers ao componente MapViewer ou carregar via rota

[ ] 9. Testar: upload de ZIP com shapefile válido
       Testar: visualização WMS no MapViewer
       Testar: desvincular layer
```

---

## 5. Pontos de Atenção

### ZIP com nome de arquivo único

O `layer_name` vem do nome do `.shp` dentro do ZIP. Se dois módulos diferentes enviarem um ZIP com o mesmo nome de arquivo interno (ex: `area_estudo.shp`), eles **vão reutilizar a mesma `MapLayer`** no banco — isso é intencional para evitar duplicatas no GeoServer.

Se o módulo precisar de isolamento total, prefixe o `layer_name`:

```php
$layerName = strtolower(str_replace(' ', '_', $modulo)) . '_' . pathinfo($shpFiles[0], PATHINFO_FILENAME);
```

### CORS — sempre use o proxy

Nunca aponte o Leaflet diretamente para `https://servicos.dnit.gov.br/...`. Use sempre a rota interna `/sgc/contratada/mapa/wms` que faz proxy no backend. Isso evita bloqueios de CORS e mantém as credenciais do GeoServer no servidor.

### GeoServer já publicado

Se a layer já foi publicada anteriormente (campo `published_at` preenchido), o sistema apenas vincula o registro existente à nova campanha sem re-fazer upload — isso é o comportamento correto e economiza banda.

### Tratamento de erro de layer duplicada no GeoServer

O GeoServer retorna HTTP 500 com mensagem `"already exists"` quando se tenta criar um datastore já existente. O controller trata esse caso como sucesso (layer existente). **Não altere esse comportamento.**

### CRS padrão

O sistema usa `EPSG:4674` (SIRGAS 2000 — padrão brasileiro) para armazenamento e `EPSG:3857` (Web Mercator) para exibição no Leaflet. O GeoServer faz a reprojeção automaticamente via WMS.

### Publicação assíncrona (futuro)

Para módulos com uploads muito frequentes, considere mover o `GeoServerService::uploadShapefileDatastore()` para um Job Laravel:

```php
dispatch(new PublicarShapefileGeoServerJob($layer, $absoluteZipPath));
```

E retornar `202 Accepted` com status `publicando`. O `MapViewer` pode verificar `published_at` antes de tentar exibir a layer.

---

## 6. Referências de Código

| Arquivo | Descrição |
|---------|-----------|
| `app/Domain/Sgc/Contratada/Produtos/Espeleologia/Controller/MapLayerController.php` | Controller de referência completo |
| `app/Domain/Sgc/Contratada/Produtos/Services/GeoServerService.php` | Serviço compartilhado de integração GeoServer |
| `app/Domain/Sgc/Contratada/Produtos/Espeleologia/Models/MapLayer.php` | Model compartilhado de layers |
| `app/Domain/Sgc/Contratada/Produtos/Espeleologia/Models/SgcEspeleoCampanhaLayer.php` | Model pivot de referência |
| `resources/js/Pages/Sgc/Contratada/Produtos/Espeleologia/ResultadosGeoserver.vue` | Componente de upload de referência |
| `resources/js/Pages/Sgc/Contratada/Produtos/Espeleologia/MapViewer.vue` | Componente de visualização de referência |
| `config/geoserver.php` | Configuração de URL e workspace |
