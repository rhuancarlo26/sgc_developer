<?php

namespace App\Domain\Modulos\Importador\Jobs;

use App\Models\ModuloImportador;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

use DateTimeInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ProcessarPlanilhaImportadorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ModuloImportador $importador;
    private Collection $erros;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $importadorId,
        private ?string $caminhoArquivo,
        private ?string $extensaoArquivo,
        private bool $temArquivo = true
    ) {
        $this->erros = collect([]);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->importador = ModuloImportador::with('modulo')->find($this->importadorId);

        if (!$this->importador) {
            return;
        }

        if ($this->temArquivo) {
            $this->processarArquivo();
        }

        $this->importador->update([
            'load' => false,
            'desc_erros' => null
        ]);
    }

    public function processarArquivo(): void
    {
        $caminhoStorageApp = 'app' . DIRECTORY_SEPARATOR . $this->caminhoArquivo;

        $arquivo = storage_path($caminhoStorageApp);

        $campos = [];
        foreach ($this->importador->modulo?->campos ?? [] as $campo) {
            $campos[$campo['nome_campo']] = [...$campo];
        }

        $this->validacaoCabecalho(arquivo: $arquivo, cabecalhosPlanilha: array_keys($campos));

        if ($this->erros->count()) {
            $errorsStr = implode('/', $this->erros->values()->all());
            throw new Exception($errorsStr);
        }

        $linha = $this->extensaoArquivo === 'xlsx' ? 1 : 0;

        DB::beginTransaction();
        (new FastExcel())->import($arquivo, function ($row) use ($campos, &$linha) {

            $linha++;

            // verificar as validações do modulo
            $dadosValidacoes = $this->validacoesCampos(
                row: $row,
                camposValidacao: $campos,
                linha: $linha
            );

            if (count($dadosValidacoes['errosRow'])) {

                $this->erros = $this->erros->concat($dadosValidacoes['errosRow']);
                return;
            }

            $dados = [...$row, ...$dadosValidacoes['dadosFormatados']];

            $this->importador->dadosJson()->create([
                'dados' => $dados
            ]);
        });

        if (count($this->erros)) {
            $errorsStr = implode('/', $this->erros->values()->all());
            DB::rollback();
            throw new Exception($errorsStr);
        }

        DB::commit();
        Storage::delete($this->caminhoArquivo);
    }

    private function validacaoCabecalho(string $arquivo, array $cabecalhosPlanilha): void
    {
        try {
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($arquivo);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $arrayColumn = $sheetData[0];
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            $this->erros->push("Existem erros na formatação da planilha ({$this->importador->nome_arquivo}), faça o download da planilha modelo!");
            return;
        }

        $diffCamposModelo = array_diff($arrayColumn, $cabecalhosPlanilha);
        $diffCamposPlanilha = array_diff($cabecalhosPlanilha, $arrayColumn);

        if (count($diffCamposModelo) || count($diffCamposPlanilha)) {
            $this->erros->push("Existem erros na formatação da planilha ({$this->importador->nome_arquivo}), faça o download da planilha modelo!");
        }
    }

    private function validacoesCampos(array $row, array $camposValidacao, int $linha): array
    {
        $errosRow = [];
        $dadosFormatados = [];

        foreach ($row as $key => $valor) {

            $camposVal = $camposValidacao[$key];

            if (
                $camposVal['obrigatorio'] &&
                ((is_string($valor) && trim($valor) === '') || $valor === null)
            ) {
                $errosRow[] = "Campo $key / Linha: $linha na planilha '{$this->importador->nome_arquivo}' é um campo obrigatório.";
                continue;
            }

            if ($camposVal['obrigatorio'] && $camposVal['regra']) {
                if (
                    $camposVal['tipo'] === 'texto' &&
                    strlen($valor) > $camposVal['max_caracteres']
                ) {
                    $errosRow[] = "Campo $key / Linha: $linha na planilha '{$this->importador->nome_arquivo}' precisa ter no máximo {$camposVal['max_caracteres']} caracteres.";
                    continue;
                }

                if (in_array($camposVal['tipo'], ['inteiro', 'decimal'])) {

                    $msgCustom = match (true) {
                        !is_numeric($valor) => "precisa ser um valor numérico.",
                        $camposVal['tipo'] === 'inteiro' && floor($valor) != $valor => "precisa ser um valor inteiro.",
                        $valor < $camposVal['valor_min'] || $valor > $camposVal['valor_max'] => "precisa estar no intervalor entre {$camposVal['valor_min']} e {$camposVal['valor_max']}.",
                        default => ''
                    };

                    if (strlen($msgCustom)) {
                        $errosRow[] = "Campo $key / Linha: $linha na planilha '{$this->importador->nome_arquivo}' {$msgCustom}";
                        continue;
                    }
                }
            }

            if ($camposVal['tipo'] === 'data') {

                $data = $this->normalizarData($valor);

                if (!$data) {
                    $errosRow[] = "Campo $key / Linha: $linha na planilha '{$this->importador->nome_arquivo}' possui uma data inválida ({$valor}).";
                    continue;
                }

                $dadosFormatados[$key] = $data;
            }
        }

        return [
            'errosRow' => $errosRow,
            'dadosFormatados' => $dadosFormatados
        ];
    }

    private function normalizarData(mixed $valor, string $formatoSaida = 'Y-m-d'): ?string
    {
        if ($valor === null) {
            return null;
        }

        if (is_string($valor)) {
            $valor = trim($valor);

            if ($valor === '') {
                return null;
            }
        }

        // 1) Já veio como objeto de data
        if ($valor instanceof DateTimeInterface) {
            return $valor->format($formatoSaida);
        }

        // 2) Veio como número serial do Excel
        // Ex.: 45398, 45398.0
        if (is_numeric($valor)) {
            try {
                $date = ExcelDate::excelToDateTimeObject((float) $valor);
                return $date->format($formatoSaida);
            } catch (\Throwable $e) {
                return null;
            }
        }

        // 3) Veio como string em formatos conhecidos
        if (is_string($valor)) {
            $formatosEntrada = [
                'd/m/Y',
                'd-m-Y',
                'd.m.Y',
                'Y-m-d',
                'Y/m/d',
                'm/d/Y',
                'm-d-Y',
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'Y-m-d H:i',
                'Y-m-d H:i:s',
                'm/d/Y H:i',
                'm/d/Y H:i:s',
            ];

            foreach ($formatosEntrada as $formato) {
                $date = DateTimeImmutable::createFromFormat($formato, $valor);

                if ($date !== false) {
                    return $date->format($formatoSaida);
                }
            }

            // 4) Tenta parse genérico
            try {
                return (new DateTimeImmutable($valor))->format($formatoSaida);
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    public function failed(\Throwable $e): void
    {
        $moduloImportador = ModuloImportador::find($this->importadorId);

        $messageError = match (true) {
            $e instanceof QueryException => 'Erro ao gravar arquivo no banco de dados!',
            $e instanceof Exception => 'Erros de validacao sobre a planilha enviada',
            default => "Erro no processamento do job - " . get_class($e)
        };

        $errorsArr = explode('/', $e->getMessage());

        Log::error($messageError, [
            'file'   => $this->caminhoArquivo,
            'error' => $errorsArr,
            'trace' => $e->getTraceAsString(),
            'importadorId' => $this->importadorId
        ]);

        if ($moduloImportador) {
            $moduloImportador->update([
                'load' => false,
                'desc_erros' => [...$errorsArr]
            ]);
        }

        if ($this->caminhoArquivo && Storage::exists($this->caminhoArquivo)) {
            Storage::delete($this->caminhoArquivo);
        }
    }
}
