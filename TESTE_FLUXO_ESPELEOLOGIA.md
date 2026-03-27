# 🧪 Teste de Fluxo End-to-End: Espeleologia Campaign-Layer Integration

## Versão: 25/03/2026
**Status Geral:** ✅ Completo - Pronto para Validação em Produção

---

## 📋 Resumo de Validação

| Componente | Status | Validação |
|-----------|--------|-----------|
| **MapLayerController::store()** | ✅ | Valida `campanha_id`, persiste via pivot table |
| **MapLayerController::index()** | ✅ | Filtra layers via JOIN na tabela pivot |
| **ResultadosGeoserver.vue** | ✅ | Envia `campanha_id` no formData |
| **MapViewer.vue** | ✅ | Recebe `campanhaId` prop, passa como query param |
| **VisualizarCampanha.vue** | ✅ | Propaga IDs para componentes filhos |
| **Database Schema** | ✅ | Pivot table `sgc_espeleo_campanha_layers` criada |
| **Routes** | ✅ | Rotas registradas (`upload_shapefile`, `index`) |

---

## 🎯 Teste 1: Upload Shapefile com Persistência

### Objetivo
Verificar que um shapefile enviado durante a criação de campanha persista com o `campanha_id` correto.

### Pré-requisitos
1. Ter uma campanha de espeleologia criada (ou criar uma)
2. Ter um arquivo `.zip` contendo um shapefile válido (`.shp`, `.shx`, `.dbf`, etc.)

### Passos

#### **Step 1: Acessar a Campanha**
```
URL: /sgc/contratada/{contrato}/produtos/espeleologia/campanhas/{campanha_id}/visualizar
```

#### **Step 2: Abrir Aba "Resultados Geoserver"**
- Clique na tab **"Resultados Geoserver"**
- Você deve ver uma interface com abas para diferentes tipos de mapas:
  - Mapa Geológico
  - Mapa Geomorfológico
  - Cavidades CECAV/SBE
  - etc.

#### **Step 3: Upload do Shapefile**
- Selecione uma aba (ex: **"Cavidades CECAV/SBE"**)
- Arraste um `.zip` contendo shapefile ou clique para selecionar
- Clique em **"Vincular Mapa"**

#### **Step 4: Verificar Backend (Database)**
Abra um terminal e execute:

```bash
cd /home/jonatas/Documentos/PROSUL/sgcdnit_out/sgc_developer

php artisan tinker --execute='
$campaigns = DB::table("sgc_espeleo_campanha_layers")->get();
foreach($campaigns as $c) {
    echo "Campanha ID: " . $c->campanha_id . " | Layer ID: " . $c->map_layer_id . " | Tipo: " . $c->tipo . "\n";
}
'
```

**Resultado Esperado:**
```
Campanha ID: X | Layer ID: Y | Tipo: cavidades
```

---

## 🎯 Teste 2: Filtro de Visualização

### Objetivo
Verificar que ao visualizar uma campanha, apenas as layers dessa campanha aparecem no mapa.

### Pré-requisitos
1. Ter pelo menos 2 campanhas
2. Cada uma com pelo menos 1 layer vinculada

### Passos

#### **Step 1: Criar Dados de Teste**
```bash
php artisan tinker

# Criar 2 campanhas
$c1 = SgcEspeleoCampanha::create(['id_contrato' => 1, 'id_campanha' => 'CAMP-001', 'status' => 'Rascunho']);
$c2 = SgcEspeleoCampanha::create(['id_contrato' => 1, 'id_campanha' => 'CAMP-002', 'status' => 'Rascunho']);

# Criar 2 layers
$l1 = MapLayer::create([...dados layer 1...]);
$l2 = MapLayer::create([...dados layer 2...]);

# Vincular layers às campanhas (diferente para cada uma)
SgcEspeleoCampanhaLayer::create(['campanha_id' => $c1->id, 'map_layer_id' => $l1->id, 'tipo' => 'geologico']);
SgcEspeleoCampanhaLayer::create(['campanha_id' => $c2->id, 'map_layer_id' => $l2->id, 'tipo' => 'cavidades']);
```

#### **Step 2: Acessar Visualização da Campanha 1**
```
URL: /sgc/contratada/{contrato}/produtos/espeleologia/campanhas/{campanha_id=c1}/visualizar
- Tab: "Resultados"
```

#### **Step 3: Inspecionar Requisição da API**
Abra **DevTools → Network**

- Procure por: `GET /sgc/contratada/espeleologia/layers?campanha_id=X`
- **Verificar Query Params:**
  ```
  campanha_id: 1  (ou o ID da campanha acessada)
  ```

#### **Step 4: Validar Resposta**
A resposta JSON deve conter **apenas** layers para a campanha filtrando:

```json
[
  {
    "id": 10,
    "layer_name": "layer_name_1",
    "workspace": "jonatas-mapas",
    "title": "Mapa Geológico",
    "tipo": "geologico"
  }
]
```

#### **Step 5: Repetir para Campanha 2**
Volte e acesse a segunda campanha → deve receber **apenas** as layers da campanha 2.

---

## 🎯 Teste 3: Validar Pivot Table Constraints

### Objetivo
Verificar que a pivot table previne duplicatas e mantém integridade referencial.

### Passo 1: Tentar Vincular a Mesma Layer Duas Vezes
```bash
php artisan tinker

$campanha_id = 1;
$layer_id = 5;

# Primeira vinculação
DB::table('sgc_espeleo_campanha_layers')->insert([
    'campanha_id' => $campanha_id,
    'map_layer_id' => $layer_id,
    'tipo' => 'geologico',
    'created_at' => now(),
    'updated_at' => now(),
]);

# Tenta inserir novamente (deve FALHAR com constraint error)
DB::table('sgc_espeleo_campanha_layers')->insert([
    'campanha_id' => $campanha_id,
    'map_layer_id' => $layer_id,
    'tipo' => 'geomorfologico',
    'created_at' => now(),
    'updated_at' => now(),
]);
```

**Resultado Esperado:**
```
SQLSTATE[23000]: Integrity constraint violation: 
1062 Duplicate entry '1-5' for key 'sgc_espeleo_campanha_layers_unique'
```

### Passo 2: Verificar Cascade Delete
```bash
php artisan tinker

# Deletar uma campanha
$campanha = SgcEspeleoCampanha::find(1);
$campanha->delete();

# Verificar que as vincuações foram deletadas automaticamente
$pivot = DB::table('sgc_espeleo_campanha_layers')->where('campanha_id', 1)->count();
echo "Remaining pivot records: " . $pivot;  // Deve ser 0
```

**Resultado Esperado:**
```
Remaining pivot records: 0
```

---

## 🔍 Verificação de Código

### Validação Rota

```bash
php artisan route:list | grep espeleologia
```

**Resultado Esperado:**
```
POST    /sgc/contratada/espeleologia/layers/upload-shapefile
GET     /sgc/contratada/espeleologia/layers
```

### Validação Model Relationships

```bash
php artisan tinker

# Validar relacionamento belongs-to-many
$campanha = SgcEspeleoCampanha::find(1);
$layers = $campanha->mapLayers;  // Deve retornar collection de layers
dd($layers);

# Validar relacionamento inverso has-many
$layer = MapLayer::find(5);
$campanhas = $layer->campanhaLayers;  // Deve retornar collection de campanhas
dd($campanhas);
```

---

## 📊 Checklist Final

Marque como completo quando cada teste passar:

- [ ] **Teste 1**: Shapefile persiste com `campanha_id` correto no banco
- [ ] **Teste 2**: Filtro de visualização retorna apenas layers da campanha
- [ ] **Teste 3**: Constraints de integridade funcionam (unique + cascade delete)
- [ ] **Verificação 1**: Rotas estão registradas corretamente
- [ ] **Verificação 2**: Relationships nos models funcionam

---

## 🚨 Troubleshooting

### Problema: "Campanha não definida para vincular camada GeoServer"
**Causa:** `props.campanhaId` está undefined no ResultadosGeoserver
**Solução:** 
1. Verificar se VisualizarCampanha está passando `:campanha-id="campanha.id"`
2. Verificar console do browser para erros

### Problema: Query retorna layers de TODAS as campanhas
**Causa:** `campanha_id` query param não chegou ao backend
**Solução:**
1. Verificar DevTools → Network → GET /espeleologia/layers
2. Confirmar query params estão sendo passados
3. Verificar MapViewer.vue lines 242-243

### Problema: SQLSTATE[23000]: Duplicate entry
**Causa:** Tentando vincular a mesma layer duas vezes
**Solução:** Implementar validação frontend (antes de enviar) ou usar `updateOrCreate` em vez de `create`

---

## 📝 Logs de Referência

Para debug detalhado, ative logs no Laravel:

```bash
# storage/logs/laravel.log
tail -f storage/logs/laravel.log | grep -i "espeleologia\|mapLayer\|campanha"
```

---

## ✅ Conclusão

Fluxo implementado e validado em código:
- ✅ Persistência: Campaign ID salvo na tabela pivot
- ✅ Filtro: API retorna apenas layers da campanha
- ✅ UI: Componentes propagam IDs corretamente
- ✅ Constraints: Integridade referencial mantida

**Próxima Etapa:** Teste manual em ambiente real com dados produção.
