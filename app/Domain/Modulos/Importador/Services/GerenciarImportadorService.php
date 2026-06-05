<?php

namespace App\Domain\Modulos\Importador\Services;

use App\Models\ModuloImportador;
use App\Models\ModuloImportadorAnexos;
use App\Models\ModuloImportadorFotos;
use Illuminate\Support\Facades\Storage;

class GerenciarImportadorService
{
    public function gerenciarFotos(ModuloImportador $importador, array $fotos): void
    {
        $idsFotos = [];

        foreach ($fotos ?? [] as $f) {
            $dataF = [];
            $metadados = [];

            if (isset($f['arquivo'])) {
                $arquivoF_ = $f['arquivo'];

                $metadados = $this->extrairMetadadosFoto($arquivoF_);

                $nomeArquivoF_ = $arquivoF_->getClientOriginalName();

                $nomeCaminhoF_ = 'Modulos_Importador'
                    . DIRECTORY_SEPARATOR
                    . 'Fotos'
                    . DIRECTORY_SEPARATOR
                    . uniqid()
                    . '_'
                    . $nomeArquivoF_;

                $arquivoF_->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminhoF_);

                $dataF['nome_arquivo'] = $nomeArquivoF_;
                $dataF['caminho_arquivo'] = $nomeCaminhoF_;
            }

            $latitude = $this->valorPreenchido($f['latitude'] ?? null)
                ? $f['latitude']
                : ($metadados['latitude'] ?? null);

            $longitude = $this->valorPreenchido($f['longitude'] ?? null)
                ? $f['longitude']
                : ($metadados['longitude'] ?? null);

            $dadosFoto = [
                'modulo_importador_id' => $importador->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'descricao' => $f['descricao'] ?? null,
                ...$dataF
            ];

            if (isset($f['arquivo'])) {
                $dadosFoto = [
                    ...$dadosFoto,
                    'data_captura' => $this->formatarDataCaptura($metadados['data_captura'] ?? null),
                    'fabricante' => $metadados['fabricante'] ?? null,
                    'modelo' => $metadados['modelo'] ?? null,
                    'largura' => $metadados['largura'] ?? null,
                    'altura' => $metadados['altura'] ?? null,
                    'orientacao' => $metadados['orientacao'] ?? null,
                    'metadados' => $metadados['metadados_completos'] ?? null,
                ];
            }

            $foto = ModuloImportadorFotos::updateOrCreate(
                ['id' => $f['id'] ?? null],
                $dadosFoto
            );

            $idsFotos[] = $foto->id;
        }

        ModuloImportadorFotos::query()
            ->where('modulo_importador_id', $importador->id)
            ->whereNotIn('id', $idsFotos)
            ->get()
            ->each(function ($item) {
                $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;

                if (Storage::exists($caminhoStorage)) {
                    Storage::delete($caminhoStorage);
                }

                $item->delete();
            });
    }

    public function gerenciarAnexos(ModuloImportador $importador, array $anexos): void
    {
        $idsAnexos = [];

        foreach ($anexos ?? [] as $a) {
            $dataA = [];

            if (isset($a['arquivo'])) {
                $arquivoA_ = $a['arquivo'];

                $nomeArquivoA_ = $arquivoA_->getClientOriginalName();

                $nomeCaminhoA_ = 'Modulos_Importador'
                    . DIRECTORY_SEPARATOR
                    . 'Anexos'
                    . DIRECTORY_SEPARATOR
                    . uniqid()
                    . '_'
                    . $nomeArquivoA_;

                $arquivoA_->storeAs('public' . DIRECTORY_SEPARATOR . $nomeCaminhoA_);

                $dataA['nome_arquivo'] = $nomeArquivoA_;
                $dataA['caminho_arquivo'] = $nomeCaminhoA_;
            }

            $anexo = ModuloImportadorAnexos::updateOrCreate(
                ['id' => $a['id'] ?? null],
                [
                    'modulo_importador_id' => $importador->id,
                    ...$dataA
                ]
            );

            $idsAnexos[] = $anexo->id;
        }

        ModuloImportadorAnexos::query()
            ->where('modulo_importador_id', $importador->id)
            ->whereNotIn('id', $idsAnexos)
            ->get()
            ->each(function ($item) {
                $caminhoStorage = 'public' . DIRECTORY_SEPARATOR . $item->caminho_arquivo;

                if (Storage::exists($caminhoStorage)) {
                    Storage::delete($caminhoStorage);
                }

                $item->delete();
            });
    }

    private function valorPreenchido($valor): bool
    {
        return !is_null($valor) && trim((string) $valor) !== '';
    }

    private function extrairMetadadosFoto($arquivo): array
    {
        if (!function_exists('exif_read_data')) {
            return [];
        }

        $caminhoTemporario = $arquivo->getRealPath();

        if (!$caminhoTemporario) {
            return [];
        }

        try {
            $exif = @exif_read_data($caminhoTemporario, 0, true);
        } catch (\Throwable $e) {
            return [];
        }

        if (!$exif) {
            return [];
        }

        $gps = $exif['GPS'] ?? [];

        $latitude = null;
        $longitude = null;

        if (!empty($gps['GPSLatitude']) && !empty($gps['GPSLatitudeRef'])) {
            $latitude = $this->converterGpsParaDecimal(
                $gps['GPSLatitude'],
                $gps['GPSLatitudeRef']
            );
        }

        if (!empty($gps['GPSLongitude']) && !empty($gps['GPSLongitudeRef'])) {
            $longitude = $this->converterGpsParaDecimal(
                $gps['GPSLongitude'],
                $gps['GPSLongitudeRef']
            );
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'data_captura' => $exif['EXIF']['DateTimeOriginal']
                ?? $exif['IFD0']['DateTime']
                ?? null,
            'fabricante' => $exif['IFD0']['Make'] ?? null,
            'modelo' => $exif['IFD0']['Model'] ?? null,
            'orientacao' => $exif['IFD0']['Orientation'] ?? null,
            'largura' => $exif['COMPUTED']['Width'] ?? null,
            'altura' => $exif['COMPUTED']['Height'] ?? null,
            'metadados_completos' => $this->limparMetadadosParaJson($exif),
        ];
    }

    private function converterGpsParaDecimal(array $coordenada, string $referencia): ?float
    {
        if (count($coordenada) < 3) {
            return null;
        }

        $graus = $this->converterParteGpsParaFloat($coordenada[0]);
        $minutos = $this->converterParteGpsParaFloat($coordenada[1]);
        $segundos = $this->converterParteGpsParaFloat($coordenada[2]);

        $decimal = $graus + ($minutos / 60) + ($segundos / 3600);

        if (in_array(strtoupper($referencia), ['S', 'W'])) {
            $decimal *= -1;
        }

        return round($decimal, 8);
    }

    private function converterParteGpsParaFloat($valor): float
    {
        if (is_string($valor) && str_contains($valor, '/')) {
            [$numerador, $denominador] = explode('/', $valor);

            if ((float) $denominador === 0.0) {
                return 0;
            }

            return (float) $numerador / (float) $denominador;
        }

        return (float) $valor;
    }

    private function formatarDataCaptura(?string $data): ?string
    {
        if (!$data) {
            return null;
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y:m:d H:i:s', substr($data, 0, 19))
                ->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function limparMetadadosParaJson($dados)
    {
        if (is_array($dados)) {
            $resultado = [];

            foreach ($dados as $chave => $valor) {
                $resultado[$chave] = $this->limparMetadadosParaJson($valor);
            }

            return $resultado;
        }

        if (is_string($dados)) {
            return mb_convert_encoding($dados, 'UTF-8', 'UTF-8');
        }

        if (is_numeric($dados) || is_bool($dados) || is_null($dados)) {
            return $dados;
        }

        return (string) $dados;
    }
}
