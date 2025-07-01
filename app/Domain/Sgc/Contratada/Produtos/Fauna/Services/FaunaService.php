<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaCampanha;
use App\Models\SgcFaunaProfissionais;
use App\Models\SgcFaunaCavernicola;
use App\Models\SgcFaunaCampanhaProfissional;
use App\Models\SgcFaunaModuloAmostral;
use App\Models\SgcFaunaQuelonios;
use App\Models\SgcFaunaMetodologia;
use App\Models\SgcFaunaResultados;
use App\Models\SgcFaunaResultadosConsideracoes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FaunaService
{
    public function salvarCampanha($contratoId, array $data)
    {
        Log::info('FaunaService: Dados recebidos para salvar campanha: ' . json_encode($data));

        $campanha = SgcFaunaCampanha::create([
            'id_contrato' => $contratoId,
            'id_campanha' => $data['id_campanha'] ?? null,
            'modulos_amostrais' => null,
            'data_ini' => $data['data_campanha_inicial'] ?? null,
            'data_fim' => $data['data_campanha_final'] ?? null,
            'periodo' => $data['periodo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'cod_emp' => $data['cod_emp'] ?? null,
            'subproduto' => $data['subproduto'] ?? null,
        ]);

        // Vincular profissionais
        if (!empty($data['profissionais'])) {
            foreach ($data['profissionais'] as $profissionalData) {
                $profissional = SgcFaunaProfissionais::where('id_contrato', $contratoId)
                    ->where('profissional', $profissionalData['profissional'])
                    ->first();
                
                if ($profissional) {
                    SgcFaunaCampanhaProfissional::create([
                        'campanha_id' => $campanha->id,
                        'id_contrato' => $contratoId,
                        'profissional_id' => $profissional->id,
                        'grupo_faunistico' => $profissionalData['grupo_faunistico'],
                    ]);
                }
            }
        }

        // Vincular módulos amostrais
        if (!empty($data['modulos_amostrais'])) {
            foreach ($data['modulos_amostrais'] as $moduloData) {
                $moduloAttributes = [
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'data_cadastro' => $moduloData['data_cadastro'] ?? null,
                    'tamanho_modulo' => $moduloData['tamanho_modulo'] ?? null,
                    'uf' => $moduloData['uf'] ?? null,
                    'municipio' => $moduloData['municipio'] ?? null,
                    'bioma' => $moduloData['bioma'] ?? null,
                    'fitofisionomia' => $moduloData['fitofisionomia'] ?? null,
                    'latitude_inicial' => $moduloData['latitude_inicial'] ?? null,
                    'longitude_inicial' => $moduloData['longitude_inicial'] ?? null,
                    'latitude_final' => $moduloData['latitude_final'] ?? null,
                    'longitude_final' => $moduloData['longitude_final'] ?? null,
                    'obs' => $moduloData['obs'] ?? null,
                ];

                // Processar upload de shapefile
                if (!empty($moduloData['arquivo'])) {
                    $file = $moduloData['arquivo'];
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('shapefiles', $filename);
                    $moduloAttributes['nome_arquivo'] = $filename;
                    $moduloAttributes['local_shape'] = $path;
                    $moduloAttributes['shape_file'] = file_get_contents($file->getRealPath());
                }

                SgcFaunaModuloAmostral::create($moduloAttributes);
            }
        }

        // Vincular pontos de quelônios e crocodilianos
        if (!empty($data['pontos_quelo_crocod']) && empty($data['nao_se_aplica'])) {
            foreach ($data['pontos_quelo_crocod'] as $pontoData) {
                SgcFaunaQuelonios::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $campanha->id,
                    'ponto_de_coleta' => $pontoData['ponto_de_coleta'] ?? null,
                    'nome_curso_hidrico' => $pontoData['nome_curso_hidrico'] ?? null,
                    'coordenadas' => $pontoData['coordenadas'] ?? null,
                    'bacia_hidrografica' => $pontoData['bacia'] ?? null,
                    'profundidade' => $pontoData['profundidade'] ?? null,
                    'largura' => $pontoData['largura'] ?? null,
                    'tipo_substrato' => $pontoData['tipo_substrato'] ?? null,
                ]);
            }
        }

        // Vincular pontos de fauna cavernícola
        if (!empty($data['pontos_cavernicola']) && !$data['nao_se_aplica']) {
            foreach ($data['pontos_cavernicola'] as $pontoData) {
                SgcFaunaCavernicola::create([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $campanha->id,
                    'cavidade' => $pontoData['cavidade'] ?? null,
                    'latitude' => $pontoData['latitude'] ?? null,
                    'longitude' => $pontoData['longitude'] ?? null,
                    'distancia_eixo_rodovia' => $pontoData['distancia_eixo_rodovia'] ?? null,
                    'formacao_associada' => $pontoData['formacao_associada'] ?? null,
                    'temperatura_media_interna' => $pontoData['temperatura_media_interna'] ?? null,
                    'temperatura_media_externa' => $pontoData['temperatura_media_externa'] ?? null,
                    'umidade_relativa_interna' => $pontoData['umidade_relativa_interna'] ?? null,
                    'umidade_relativa_externa' => $pontoData['umidade_relativa_externa'] ?? null,
                ]);
            }
        }

        // Vincular metodologias
        if (!empty($data['metodologias'])) {
            foreach ($data['metodologias'] as $metodologiaData) {
                SgcFaunaMetodologia::create([
                    'campanha_id' => $campanha->id,
                    'id_contrato' => $contratoId,
                    'grupo_faunistico' => $metodologiaData['grupo_faunistico'],
                    'metodologia' => $metodologiaData['metodologia'],
                ]);
            }
        }

        // Salvar considerações
        if (!empty($data['consideracoes'])) {
            SgcFaunaResultadosConsideracoes::create([
                'id_contrato' => $contratoId,
                'id_campanha' => $campanha->id,
                'consideracoes' => $data['consideracoes'],
            ]);
        }

        Log::info('FaunaService: Campanha salva com ID: ' . $campanha->id);
        return $campanha->id;
    }

    public function salvarProfissional($contratoId, array $data)
    {
        Log::info('FaunaService: Salvando profissional para contrato ID: ' . $contratoId);
        $profissional = SgcFaunaProfissionais::create([
            'id_contrato' => $contratoId,
            'profissional' => $data['profissional'],
            'formacao' => $data['formacao'],
            'telefone' => $data['telefone'] ?? null,
            'cpf' => $data['cpf'] ?? null,
            'email' => $data['email'] ?? null,
            'curriculum_lattes' => $data['curriculum_lattes'] ?? null,
            'funcao' => $data['funcao'] ?? null,
            'ctf' => $data['ctf'] ?? null,
            'validade' => $data['validade'] ?? null,
            'conselho_de_classe' => $data['conselho_de_classe'],
            'numero_de_registro' => $data['numero_de_registro'] ?? null,
            'status' => $data['status'],
            'observacao' => $data['observacao'] ?? null,
        ]);
        Log::info('FaunaService: Profissional salvo com ID: ' . $profissional->id);
        return $profissional;
    }

    public function salvarResultados($contratoId, $file)
    {
        Log::info('FaunaService: Processando planilha de resultados para contrato ID: ' . $contratoId);

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $expectedHeaders = [
                'ID Campanha', 'Módulo', 'Parcela', 'ID Armadilha', 'Grupo Amostrado', 'Data do Registro', 'Hora do Registro',
                'Categoria', 'Classe', 'Ordem', 'Família', 'Gênero', 'Espécie', 'Nome Comum', 'Sexo', 'Faixa Etária',
                'Qnt de Indivíduos', 'Num Marcação', 'Coletado', 'Num de Tombamento', 'Dados Biométricos', 'Comp total',
                'Cabeça', 'Cauda', 'Fêmur', 'Orelha', 'Peso', 'Status Conservação Federal', 'Status Conservação IUCN'
            ];

            $headerRow = array_map('trim', array_shift($rows));
            if ($headerRow !== $expectedHeaders) {
                throw new \Exception('Cabeçalho da planilha inválido. Use o modelo fornecido.');
            }

            foreach ($rows as $row) {
                // Converter data (DD/MM/YYYY)
                $dataRegistro = null;
                if ($row[5]) {
                    $dateTime = \DateTime::createFromFormat('d/m/Y', trim($row[5]));
                    if (!$dateTime) {
                        throw new \Exception('Formato de data inválido: ' . $row[5]);
                    }
                    $dataRegistro = $dateTime->format('Y-m-d');
                }

                // Converter hora (HH:MM)
                $horaRegistro = null;
                if ($row[6]) {
                    $dateTime = \DateTime::createFromFormat('H:i', trim($row[6]));
                    if (!$dateTime) {
                        throw new \Exception('Formato de hora inválido: ' . $row[6]);
                    }
                    $horaRegistro = $dateTime->format('H:i:s');
                }

                $data = [
                    'id_contrato' => $contratoId,
                    'id_campanha' => $row[0] ?? null,
                    'modulo' => $row[1] ?? null,
                    'parcela' => $row[2] ?? null,
                    'id_armadilha' => $row[3] ?? null,
                    'grupo_amostrado' => $row[4] ?? null,
                    'data_registro' => $dataRegistro,
                    'hora_registro' => $horaRegistro,
                    'categoria' => $row[7] ?? null,
                    'classe' => $row[8] ?? null,
                    'ordem' => $row[9] ?? null,
                    'familia' => $row[10] ?? null,
                    'genero' => $row[11] ?? null,
                    'especie' => $row[12] ?? null,
                    'nome_comum' => $row[13] ?? null,
                    'sexo' => $row[14] ?? null,
                    'faixa_etaria' => $row[15] ?? null,
                    'qnt_individuos' => $row[16] ?? 0,
                    'num_marcacao' => $row[17] ?? null,
                    'coletado' => $row[18] ?? null,
                    'num_tombamento' => $row[19] ?? null,
                    'dados_biometricos' => $row[20] ?? null,
                    'comp_total' => $row[21] ?? null,
                    'cabeca' => $row[22] ?? null,
                    'cauda' => $row[23] ?? null,
                    'femur' => $row[24] ?? null,
                    'orelha' => $row[25] ?? null,
                    'peso' => $row[26] ?? null,
                    'status_conservacao_federal' => $row[27] ?? null,
                    'status_conservacao_iucn' => $row[28] ?? null,
                ];

                if ($data['id_campanha'] && !SgcFaunaCampanha::where('id', $data['id_campanha'])->exists()) {
                    throw new \Exception('ID de campanha inválido: ' . $data['id_campanha']);
                }

                // Verificar duplicação
                $exists = SgcFaunaResultados::where([
                    'id_contrato' => $contratoId,
                    'id_campanha' => $data['id_campanha'],
                    'modulo' => $data['modulo'],
                    'parcela' => $data['parcela'],
                    'id_armadilha' => $data['id_armadilha'],
                    'data_registro' => $data['data_registro'],
                    'hora_registro' => $data['hora_registro'],
                    'especie' => $data['especie'],
                ])->exists();

                if ($exists) {
                    Log::warning('FaunaService: Registro duplicado ignorado.', $data);
                    continue;
                }

                SgcFaunaResultados::create($data);
            }

            Log::info('FaunaService: Resultados salvos com sucesso para contrato ID: ' . $contratoId);
        } catch (\Exception $e) {
            Log::error('FaunaService: Erro ao processar planilha de resultados', [
                'contrato' => $contratoId,
                'erro' => $e->getMessage(),
            ]);
            throw new \Exception('Erro ao salvar resultados: ' . $e->getMessage());
        }
    }

    public function getProfissionaisByContrato($contratoId)
    {
        return SgcFaunaProfissionais::where('id_contrato', $contratoId)
            ->get(['id', 'profissional', 'formacao'])
            ->toArray();
    }
}