<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use App\Models\SgcFaunaResultadosConsideracoes;
use App\Models\SgcFaunaResultadosTerrestre;
use App\Models\SgcFaunaResultadosAquatica;
use App\Models\SgcFaunaResultadosCavernicola;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultadoService
{
    /**
     * Salva resultados a partir dos 3 modelos padrão (Terrestre, Aquática, Cavernícola).
     * $tipoPlanilha deve ser: 'terrestre' | 'aquatica' | 'cavernicola'
     */
    public function salvarResultados(int $contratoId, $file, ?int $campanhaId = null, ?string $consideracoes = null, ?string $tipoPlanilha = null)
    {
        if (!$file || !$file->isValid()) {
            throw new \Exception('Arquivo de planilha inválido.');
        }

        // Carrega planilha
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet   = $spreadsheet->getActiveSheet();
        $rows        = $worksheet->toArray(null, true, true, true); // preserva vazios

        if (empty($rows) || count($rows) < 2) {
            throw new \Exception('Planilha vazia ou sem dados.');
        }

        // 1) Descobrir a linha de cabeçalho (pega a primeira com >= 3 células não vazias)
        $headerIdx = null;
        foreach ($rows as $i => $row) {
            $nonEmpty = 0;
            foreach ($row as $cell) {
                if (trim((string)$cell) !== '') $nonEmpty++;
            }
            if ($nonEmpty >= 3) { $headerIdx = $i; break; }
        }
        if ($headerIdx === null) {
            throw new \Exception('Não foi possível identificar o cabeçalho.');
        }

        // 2) Array com os títulos do cabeçalho (com normalização)
        $headerRaw = array_values($rows[$headerIdx]); // zera chaves
        $headerMap = $this->buildHeaderIndexMap($headerRaw); // normalizado => índice

        // 3) Se não veio $tipoPlanilha do front, tenta inferir pelos campos obrigatórios
        if (!$tipoPlanilha) {
            $tipoPlanilha = $this->inferirTipoPlanilhaPelosHeaders($headerMap);
        }

        // 4) Validar cabeçalho mínimo por tipo
        $this->validarHeadersObrigatorios($tipoPlanilha, $headerMap, $headerRaw);

        // 5) Apagar registros anteriores (se campanhaId fornecido)
        if ($campanhaId) {
            $this->deleteByCampanha($tipoPlanilha, $campanhaId);
        }

        // 6) Percorrer as linhas de dados (tudo depois do cabeçalho)
        $recordsSaved = 0;
        for ($r = $headerIdx + 1; $r <= count($rows); $r++) {
            $row = array_values($rows[$r] ?? []);
            if ($this->linhaVazia($row)) continue;

            // Mapeia a linha conforme o tipo escolhido
            switch ($tipoPlanilha) {
                case 'terrestre':
                    $payload = $this->mapRowTerrestre($row, $headerMap, $contratoId, $campanhaId);
                    if ($payload) { SgcFaunaResultadosTerrestre::create($payload); $recordsSaved++; }
                    break;

                case 'aquatica':
                    $payload = $this->mapRowAquatica($row, $headerMap, $contratoId, $campanhaId);
                    if ($payload) { SgcFaunaResultadosAquatica::create($payload); $recordsSaved++; }
                    break;

                case 'cavernicola':
                    $payload = $this->mapRowCavernicola($row, $headerMap, $contratoId, $campanhaId);
                    if ($payload) { SgcFaunaResultadosCavernicola::create($payload); $recordsSaved++; }
                    break;
            }
        }

        // 7) Considerações
        if ($campanhaId && $consideracoes !== null) {
            SgcFaunaResultadosConsideracoes::updateOrCreate(
                ['id_campanha' => $campanhaId, 'id_contrato' => $contratoId],
                ['consideracoes' => $consideracoes]
            );
        }

        return [
            'success' => true,
            'message' => "Resultados salvos com sucesso. {$recordsSaved} registros importados."
        ];
    }

    /* =========================
     * VALIDADORES E HELPERS
     * ========================= */

    private function deleteByCampanha(string $tipo, int $campanhaId): void
    {
        if ($tipo === 'terrestre') {
            SgcFaunaResultadosTerrestre::where('id_campanha', $campanhaId)->delete();
        } elseif ($tipo === 'aquatica') {
            SgcFaunaResultadosAquatica::where('id_campanha', $campanhaId)->delete();
        } else {
            SgcFaunaResultadosCavernicola::where('id_campanha', $campanhaId)->delete();
        }
    }

    private function buildHeaderIndexMap(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $label) {
            $norm = $this->norm((string)$label);
            if ($norm !== '') $map[$norm] = $idx;
        }
        return $map;
    }

    private function norm(string $s): string
    {
        $s = mb_strtolower(trim($s));
        // remover acentos
        $s = iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$s);
        // normalizações comuns
        $s = str_replace(
            ['  ',' (ua)',' (pa)','/','-','  '],
            [' ','','',' ',' ',' '],
            $s
        );
        $s = preg_replace('/\s+/', ' ', $s);
        return $s;
    }

    private function linhaVazia(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') return false;
        }
        return true;
    }

    private function getCell(array $row, array $headerMap, string $headerNorm): ?string
    {
        if (!array_key_exists($headerNorm, $headerMap)) return null;
        $idx = $headerMap[$headerNorm];
        return isset($row[$idx]) ? trim((string)$row[$idx]) : null;
    }

    private function toDateYmd(?string $value): ?string
    {
        if ($value === null || $value === '') return null;

        // Excel serial?
        if (is_numeric($value)) {
            try {
                $base = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$value);
                return $base->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

        // d/m/Y ou d-m-Y
        foreach (['d/m/Y','d-m-Y','Y-m-d','Y/m/d'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            if ($dt) return $dt->format('Y-m-d');
        }
        return null;
    }

    private function toTimeHis(?string $value): ?string
    {
        if ($value === null || $value === '') return null;

        if (is_numeric($value)) {
            // Excel time serial
            try {
                $secs = (float)$value * 24 * 3600;
                return gmdate('H:i:s', (int)$secs);
            } catch (\Throwable $e) {}
        }
        // H:i ou H:i:s
        foreach (['H:i:s','H:i'] as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $value);
            if ($dt) return $dt->format('H:i:s');
        }
        return null;
    }

    // private function toDecimal(?string $value): ?float
    // {
    //     if ($value === null || $value === '') return null;
    //     // aceita "12,34" e "12.34"
    //     $v = str_replace(['.', ','], ['', '.'], trim($value)); // remove milhar e usa ponto como decimal
    //     if (!is_numeric($v)) return null;
    //     return (float)$v;
    // }

    private function toDecimal(?string $value): ?float
    {
        if ($value === null) return null;

        // Se já for número (Excel às vezes envia float puro)
        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim($value);
        if ($value === '') return null;

        // Remove espaços
        $value = str_replace(' ', '', $value);

        // Caso padrão brasileiro:  -4,801393727
        // Se tiver vírgula E não tiver ponto → troca vírgula por ponto
        if (str_contains($value, ',') && !str_contains($value, '.')) {
            $value = str_replace(',', '.', $value);
        }

        // Agora só aceita se for numérico
        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }


    private function toInt(?string $value): ?int
    {
        if ($value === null || $value === '') return null;
        if (!is_numeric($value)) return null;
        return (int)$value;
    }

    private function validarHeadersObrigatorios(string $tipo, array $headerMap, array $headerRaw): void
    {
        $obrigatorios = [];
        if ($tipo === 'terrestre') {
            $obrigatorios = [
                'campanha','estacao do ano','data','horario','municipio',
                'unidade amostral','ponto amostral','latitude','longitude',
                'classe','ordem','familia','genero','especie', 'abundancia'
            ];
        } elseif ($tipo === 'aquatica') {
            $obrigatorios = [
                'campanha','estacao do ano','data','horario','municipio',
                'unidade amostral','ponto amostral','latitude','longitude',
                'tipo de ambiente','largura media (rio)','profundidade media',
                'classe','ordem','familia','genero','especie', 'abundancia'
            ];
        } else { // cavernicola
            $obrigatorios = [
                'caverna','campanha','estacao do ano','data','horario','municipio',
                'unidade amostral','ponto amostral','latitude','longitude',
                'classe','ordem','familia','genero','especie', 'abundancia'
            ];
        }

        $faltando = [];
        foreach ($obrigatorios as $h) {
            if (!array_key_exists($h, $headerMap)) $faltando[] = $h;
        }
        if ($faltando) {
            // Ajuda debug: devolve o cabeçalho lido
            throw new \Exception(
                'Cabeçalho inválido para o modelo ' . strtoupper($tipo) .
                '. Faltando: ' . implode(', ', $faltando) .
                '. Verifique se usou o modelo correto.'
            );
        }
    }

    private function inferirTipoPlanilhaPelosHeaders(array $headerMap): string
    {
        $hasTerrestre = array_key_exists('habitat', $headerMap) || array_key_exists('fitofisionomia', $headerMap);
        $hasAquatica  = array_key_exists('tipo de ambiente', $headerMap) || array_key_exists('largura media (rio)', $headerMap);
        $hasCaverna   = array_key_exists('caverna', $headerMap) || array_key_exists('substrato amostrado', $headerMap);

        if ($hasCaverna) return 'cavernicola';
        if ($hasAquatica) return 'aquatica';
        if ($hasTerrestre) return 'terrestre';

        // fallback
        return 'terrestre';
    }

    /* =========================
     * MAPEAMENTOS POR MODELO
     * ========================= */

    private function mapRowTerrestre(array $row, array $H, int $contratoId, ?int $campanhaId): ?array
    {
        // Nomes conforme planilha Terrestre
        $get = fn($label) => $this->getCell($row, $H, $label);

        $data = [
            'id_contrato'           => $contratoId,
            'id_campanha'           => $campanhaId ?? null,

            'campanha'              => $get('campanha'),
            'estacao_do_ano'        => $get('estacao do ano'),
            'data'                  => $this->toDateYmd($get('data')),
            'periodo'               => $get('periodo'),
            'horario'               => $this->toTimeHis($get('horario')),
            'condicao_climatica'    => $get('condicao climatica'),
            'temperatura'           => $this->toDecimal($get('temperatura')),
            'pluviosidade'          => $this->toDecimal($get('pluviosidade')),
            'municipio'             => $get('municipio'),
            'unidade_amostral'      => $get('unidade amostral'),
            'ponto_amostral'        => $get('ponto amostral'),
            'datum'                 => $get('datum'),
            'latitude'              => $this->toDecimal($get('latitude')),
            'longitude'             => $this->toDecimal($get('longitude')),
            'metodologia'           => $get('metodologia'),
            'tipo_metodologia'      => $get('tipo de metodologia'),
            'fitofisionomia'        => $get('fitofisionomia'),
            'habitat'               => $get('habitat preferencial'),
            'caracteristicas_ponto' => $get('caracteristicas do ponto amostral'),

            'classe'                => $get('classe'),
            'ordem'                 => $get('ordem'),
            'familia'               => $get('familia'),
            'genero'                => $get('genero'),
            'especie'               => $get('especie'),
            'nome_cientifico'       => $get('nome cientifico'),
            'nome_comum'            => $get('nome comum'),
            'abundancia'            => $this->toInt($get('abundancia')),
            'numero_de_registros'   => $this->toInt($get('numero de registros')),
            'sensibilidade'         => $get('sensibilidade'),
            'endemismo'             => $get('endemismo'),
            'observacao'            => $get('observacao'),
            'iucn'                  => $get('iucn'),
            'mma'                   => $get('mma'),
            'salve'                 => $get('salve'),
            'estado'                => $get('estado'),
            'pan_prim'              => $get('pan'),
            'registro_fotografico'  => $get('registro fotografico'),
            'coletado'              => $get('coletadato') ?? $get('coletado'),
            'numero_tombo'          => $get('numero tombo'),
        ];

        // linha toda vazia?
        $verifica = array_filter($data, fn($v) => $v !== null && $v !== '');
        return $verifica ? $data : null;
    }

    private function mapRowAquatica(array $row, array $H, int $contratoId, ?int $campanhaId): ?array
    {
        $get = fn($label) => $this->getCell($row, $H, $label);

        $data = [
            'id_contrato'                  => $contratoId,
            'id_campanha'                  => $campanhaId ?? null,

            'campanha'                     => $get('campanha'),
            'estacao_do_ano'               => $get('estacao do ano'),
            'data'                         => $this->toDateYmd($get('data')),
            'periodo'                      => $get('periodo'),
            'horario'                      => $this->toTimeHis($get('horario')),
            'condicao_climatica'           => $get('condicao climatica'),
            'temperatura'                  => $this->toDecimal($get('temperatura')),
            'pluviosidade'                 => $this->toDecimal($get('pluviosidade')),
            'municipio'                    => $get('municipio'),
            'unidade_amostral'             => $get('unidade amostral'),
            'ponto_amostral'               => $get('ponto amostral'),
            'datum'                        => $get('datum'),
            'latitude'                     => $this->toDecimal($get('latitude')),
            'longitude'                    => $this->toDecimal($get('longitude')),
            'metodologia'                  => $get('metodologia'),
            'tipo_metodologia'             => $get('tipo de metodologia'),
            'fitofisionomia'               => $get('fitofisionomia'),
            'habitat_preferencial'         => $get('habitat preferencial'),
            'tipo_ambiente'                => $get('tipo de ambiente'),
            'largura_media_rio'            => $this->toDecimal($get('largura media (rio)')),
            'profundidade_media'           => $this->toDecimal($get('profundidade media')),
            'tipo_substrato'               => $get('tipo de substrato'),
            'caracteristicas_agua'         => $get('caracteristicas da agua'),
            'caracteristicas_entorno_ponto'   => $get('caracteristicas do ponto amostral'),

            'classe'                       => $get('classe'),
            'ordem'                        => $get('ordem'),
            'familia'                      => $get('familia'),
            'genero'                       => $get('genero'),
            'especie'                      => $get('especie'),
            'nome_cientifico'              => $get('nome cientifico'),
            'nome_comum'                   => $get('nome comum'),
            'abundancia'                   => $this->toInt($get('abundancia')),
            'numero_de_registros'          => $this->toInt($get('numero de registros')),
            'sensibilidade'                => $get('sensibilidade'),
            'endemismo'                    => $get('endemismo'),
            'observacao'                   => $get('observacao'),
            'iucn'                         => $get('iucn'),
            'mma'                          => $get('mma'),
            'salve'                        => $get('salve'),
            'estado'                       => $get('estado'),
            'pan_prim'                     => $get('pan'),
            'registro_fotografico'         => $get('registro fotografico'),
            'coletado'                     => $get('coletadato') ?? $get('coletado'),
            'numero_tombo'                 => $get('numero tombo (colecao)'),
        ];

        $verifica = array_filter($data, fn($v) => $v !== null && $v !== '');
        return $verifica ? $data : null;
    }

    private function mapRowCavernicola(array $row, array $H, int $contratoId, ?int $campanhaId): ?array
    {
        $get = fn($label) => $this->getCell($row, $H, $label);

        $data = [
            'id_contrato'                   => $contratoId,
            'id_campanha'                   => $campanhaId ?? null,

            'caverna'                       => $get('caverna'),
            'campanha'                      => $get('campanha'),
            'estacao_do_ano'                => $get('estacao do ano'),
            'data'                          => $this->toDateYmd($get('data')),
            'periodo'                       => $get('periodo'),
            'horario'                       => $this->toTimeHis($get('horario')),
            'condicao_climatica'            => $get('condicao climatica'),
            'temperatura'                   => $this->toDecimal($get('temperatura')),
            'pluviosidade'                  => $this->toDecimal($get('pluviosidade')),
            'municipio'                     => $get('municipio'),
            'unidade_amostral'              => $get('unidade amostral'),
            'ponto_amostral'                => $get('ponto amostral'),
            'datum'                         => $get('datum'),
            'latitude'                      => $this->toDecimal($get('latitude')),
            'longitude'                     => $this->toDecimal($get('longitude')),
            'metodologia'                   => $get('metodologia'),
            'tipo_metodologia'              => $get('tipo de metodologia'),
            'fitofisionomia'                => $get('fitofisionomia'),

            'substrato_amostrado'           => $get('substrato amostrado'),
            'caracteristicas_entorno_ponto'    => $get('caracteristicas de entorno do ponto amostral'),

            'classe'                        => $get('classe'),
            'ordem'                         => $get('ordem'),
            'familia'                       => $get('familia'),
            'genero'                        => $get('genero'),
            'especie'                       => $get('especie'),
            'nome_cientifico'               => $get('nome cientifico'),
            'nome_comum'                    => $get('nome comum'),
            'abundancia'                    => $this->toInt($get('abundancia')),
            'numero_de_registros'           => $this->toInt($get('numero de registros')),
            'categoria_ecologica'           => $get('categoria ecologica'),
            'sensibilidade'                 => $get('sensibilidade'),
            'endemismo'                     => $get('endemismo'),
            'observacao'                    => $get('observacao'),

            'presenca_guano'                => $get('presenca de guano'),
            'presenca_agua'                 => $get('presenca de agua'),
            'conectividade_externa'         => $get('conectividade externa'),
            'perturbacao_antropica'         => $get('perturbacao antropica'),

            'iucn'                          => $get('iucn'),
            'mma'                           => $get('mma'),
            'salve'                         => $get('salve'),
            'estado'                        => $get('estado'),
            'pan_prim'                      => $get('pan'),
            'registro_fotografico'          => $get('registro fotografico'),
            'coletado'                      => $get('coletadato') ?? $get('coletado'),
            'numero_tombo'                  => $get('numero tombo (colecao)'),
        ];

        $verifica = array_filter($data, fn($v) => $v !== null && $v !== '');
        return $verifica ? $data : null;
    }
}
