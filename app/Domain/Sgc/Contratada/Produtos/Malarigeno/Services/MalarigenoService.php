<?php

namespace App\Domain\Sgc\Contratada\Produtos\Malarigeno\Services;

use App\Models\SgcModuloCampanha;
use App\Models\SgcModuloCampanhaFoto;
use App\Models\SgcModuloCampanhaAnexo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MalarigenoService
{
    public function store(array $data): SgcModuloCampanha
    {
        $arquivo = $data['arquivo'] ?? null;
        $fotos = $data['fotos'] ?? [];
        $anexos = $data['anexos'] ?? [];
        $enviarAnalise = filter_var($data['enviar_analise'] ?? false, FILTER_VALIDATE_BOOLEAN);

        unset($data['arquivo'], $data['fotos'], $data['anexos'], $data['enviar_analise']);

        if (!empty($data['contrato_id'])) {
            $data['id_contrato'] = $data['contrato_id'];
            unset($data['contrato_id']);
        }

        if ($arquivo instanceof UploadedFile) {
            $nomeArquivo = $arquivo->getClientOriginalName();
            $nomeUnico = uniqid() . '_' . $nomeArquivo;
            $caminho = $arquivo->storeAs('Malarigeno/Planilhas', $nomeUnico, 'public');

            $data['planilha_nome'] = $nomeArquivo;
            $data['planilha_caminho'] = $caminho;
        }

        $data['status'] = $enviarAnalise ? 'Em análise' : 'Em elaboração';
        $data['produto'] = $data['produto'] ?? 'malarigeno';

        $malarigeno = SgcModuloCampanha::create($data);

        try {
            $this->gerenciarFotos($malarigeno, $fotos);
            $this->gerenciarAnexos($malarigeno, $anexos);

            return $malarigeno;
        } catch (\Throwable $e) {
            $malarigeno->fotos()->delete();
            $malarigeno->anexos()->delete();
            $malarigeno->delete();

            throw $e;
        }
    }

    private function gerenciarFotos(SgcModuloCampanha $malarigeno, array $fotos): void
    {
        $idsFotos = [];

        foreach ($fotos as $f) {
            $metadados = [];
            $dataF = [];

            if (!empty($f['arquivo']) && $f['arquivo'] instanceof UploadedFile) {
                $arquivoF = $f['arquivo'];
                $metadados = $this->extrairMetadadosFoto($arquivoF);

                $nomeArquivoF = $arquivoF->getClientOriginalName();
                $nomeUnico = uniqid() . '_' . $nomeArquivoF;
                $caminho = $arquivoF->storeAs('Malarigeno/Fotos', $nomeUnico, 'public');

                $dataF['nome_arquivo'] = $nomeArquivoF;
                $dataF['caminho_arquivo'] = $caminho;
            }

            $latitude = $this->valorPreenchido($f['latitude'] ?? null)
                ? $f['latitude']
                : ($metadados['latitude'] ?? null);

            $longitude = $this->valorPreenchido($f['longitude'] ?? null)
                ? $f['longitude']
                : ($metadados['longitude'] ?? null);

            $dadosFoto = [
                'campanha_id' => $malarigeno->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'descricao' => $f['descricao'] ?? null,
                ...$dataF,
            ];

            if (!empty($f['arquivo']) && $f['arquivo'] instanceof UploadedFile) {
                $dadosFoto['data_captura'] = $this->formatarDataCaptura($metadados['data_captura'] ?? null);
                $dadosFoto['metadados'] = $metadados['metadados_completos'] ?? null;
            }

            $foto = SgcModuloCampanhaFoto::updateOrCreate(
                ['id' => $f['id'] ?? null],
                $dadosFoto
            );

            $idsFotos[] = $foto->id;
        }

        SgcModuloCampanhaFoto::query()
            ->where('campanha_id', $malarigeno->id)
            ->when(count($idsFotos), fn($query) => $query->whereNotIn('id', $idsFotos))
            ->when(count($idsFotos) === 0, fn($query) => $query)
            ->delete();
    }

    private function gerenciarAnexos(SgcModuloCampanha $malarigeno, array $anexos): void
    {
        $idsAnexos = [];

        foreach ($anexos as $a) {
            $dataA = [];

            if (!empty($a['arquivo']) && $a['arquivo'] instanceof UploadedFile) {
                $arquivoA = $a['arquivo'];
                $nomeArquivoA = $arquivoA->getClientOriginalName();
                $nomeUnico = uniqid() . '_' . $nomeArquivoA;
                $caminho = $arquivoA->storeAs('Malarigeno/Anexos', $nomeUnico, 'public');

                $dataA['nome_arquivo'] = $nomeArquivoA;
                $dataA['caminho_arquivo'] = $caminho;
            }

            $anexo = SgcModuloCampanhaAnexo::updateOrCreate(
                ['id' => $a['id'] ?? null],
                [
                    'campanha_id' => $malarigeno->id,
                    ...$dataA,
                ]
            );

            $idsAnexos[] = $anexo->id;
        }

        SgcModuloCampanhaAnexo::query()
            ->where('campanha_id', $malarigeno->id)
            ->when(count($idsAnexos), fn($query) => $query->whereNotIn('id', $idsAnexos))
            ->when(count($idsAnexos) === 0, fn($query) => $query)
            ->delete();
    }

    private function valorPreenchido($valor): bool
    {
        return !is_null($valor) && trim((string) $valor) !== '';
    }

    private function extrairMetadadosFoto(UploadedFile $arquivo): array
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
            $latitude = $this->converterGpsParaDecimal($gps['GPSLatitude'], $gps['GPSLatitudeRef']);
        }

        if (!empty($gps['GPSLongitude']) && !empty($gps['GPSLongitudeRef'])) {
            $longitude = $this->converterGpsParaDecimal($gps['GPSLongitude'], $gps['GPSLongitudeRef']);
        }

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'data_captura' => $exif['EXIF']['DateTimeOriginal'] ?? $exif['IFD0']['DateTime'] ?? null,
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
                return 0.0;
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
