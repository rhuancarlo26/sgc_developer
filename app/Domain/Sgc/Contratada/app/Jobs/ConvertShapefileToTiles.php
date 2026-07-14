<?php

namespace App\Domain\Sgc\Contratada\app\Jobs;

use App\Models\SgcvwLayer as Layer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ConvertShapefileToTiles implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $layer;
    public $timeout = 3600; // 1 hora para shapefiles muito grandes

    public function __construct(Layer $layer)
    {
        $this->layer = $layer;
    }

    public function handle()
    {
        ini_set('memory_limit', '8G'); // Aumenta limite de memória PHP para datasets grandes

        Log::info("Starting conversion for layer {$this->layer->id}");

        $this->layer->update(['status' => 'processing', 'error' => null]);

        $zipPath = storage_path('app/' . $this->layer->file_path);
        $workDir = storage_path("app/tmp/layer_{$this->layer->id}_" . time());
        $tilesDir = storage_path('app/tiles');

        if (!is_dir($workDir) && !mkdir($workDir, 0777, true) && !is_dir($workDir)) {
            $this->failWith("Failed to create workdir: {$workDir}");
            return;
        }
        if (!is_dir($tilesDir) && !mkdir($tilesDir, 0777, true) && !is_dir($tilesDir)) {
            $this->failWith("Failed to create tilesDir: {$tilesDir}");
            return;
        }

        // 1) Extrair ZIP
        $zip = new ZipArchive();
        if (!file_exists($zipPath)) {
            $this->failWith("Zip file not found: {$zipPath}");
            $this->cleanup($workDir);
            return;
        }

        if ($zip->open($zipPath) !== true) {
            $this->failWith("Cannot open zip: {$zipPath}");
            $this->cleanup($workDir);
            return;
        }

        if (!$zip->extractTo($workDir)) {
            $zip->close();
            $this->failWith("Failed to extract zip to: {$workDir}");
            $this->cleanup($workDir);
            return;
        }
        $zip->close();

        // 2) Encontrar .shp (caso haja subdirs, procura recursivamente)
        $shp = $this->findShapefile($workDir);
        if (!$shp) {
            $this->failWith("No .shp found in extracted zip: {$zipPath}");
            $this->cleanup($workDir);
            return;
        }

        $shpFull = $shp;
        $shpSize = filesize($shpFull) / (1024 * 1024); // MB
        Log::info("Extracted to {$workDir}, found shp: {$shpFull} (size: {$shpSize} MB)");

        // 3) Converter .shp para GeoJSON usando ogr2ogr (para compatibilidade com tippecanoe)
        $geojsonName = Str::slug($this->layer->name) . ".geojson";
        $geojsonPath = $workDir . DIRECTORY_SEPARATOR . $geojsonName;

        $ogrBin = trim(shell_exec('which ogr2ogr') ?: '/usr/bin/ogr2ogr');
        if (!file_exists($ogrBin)) {
            $this->failWith("ogr2ogr not found at: {$ogrBin}. Install GDAL or set the correct path.");
            $this->cleanup($workDir);
            return;
        }

        // $ogrCmd = [
        //     $ogrBin,
        //     '-f', 'GeoJSON',
        //     $geojsonPath,
        //     $shpFull,
        //     '-t_srs', 'EPSG:4326' // Garante projeção WGS84, assumida pelo tippecanoe
        // ];
        $ogrCmd = [
            $ogrBin,
            '-f', 'GeoJSON',
            $geojsonPath,
            $shpFull,
            '-t_srs', 'EPSG:4326',
            '-lco', 'ENCODING=UTF-8',
            '-lco', 'RFC7946=YES',
            '-mapFieldType', 'All=String'
        ];

        $ogrProcess = new Process($ogrCmd, null, null, null, $this->timeout);

        try {
            $ogrProcess->mustRun(function ($type, $buffer) {
                Log::info($type === Process::OUT ? 'OGR OUT: ' . $buffer : 'OGR ERR: ' . $buffer);
            });
            $geojsonSize = filesize($geojsonPath) / (1024 * 1024); // MB
            Log::info("Converted shp to GeoJSON: {$geojsonPath} (size: {$geojsonSize} MB)");
            $cleanedPath = $workDir . "/cleaned.geojson";
            $iconvCmd = "iconv -f UTF-8 -t UTF-8 -c " . escapeshellarg($geojsonPath) .
                        " > " . escapeshellarg($cleanedPath);

            shell_exec($iconvCmd);

            if (file_exists($cleanedPath) && filesize($cleanedPath) > 10) {
                $geojsonPath = $cleanedPath; // substitui o original
            }
            
        } catch (ProcessFailedException $e) {
            $err = "ogr2ogr failed. ExitCode: " . $ogrProcess->getExitCode() . PHP_EOL .
                   "Cmd: " . implode(' ', $ogrCmd) . PHP_EOL .
                   "Stdout: " . $ogrProcess->getOutput() . PHP_EOL .
                   "Stderr: " . $ogrProcess->getErrorOutput();
            $this->failWith($err);
            $this->cleanup($workDir);
            return;
        }

        // 4) Comando tippecanoe - usar o GeoJSON como input
        $mbtilesName = Str::slug($this->layer->name) . ".mbtiles";
        $output = $tilesDir . DIRECTORY_SEPARATOR . $mbtilesName;

        $tippecanoeBin = trim(shell_exec('which tippecanoe') ?: '/usr/local/bin/tippecanoe');
        if (!file_exists($tippecanoeBin)) {
            $this->failWith("tippecanoe not found at: {$tippecanoeBin}. Install tippecanoe or set the correct path.");
            $this->cleanup($workDir);
            return;
        }

        // Opções otimizadas para arquivos grandes: zoom auto, drop densidade, simplificação maior, limite de tile size, parallel read, max zoom 18, force para sobrescrita
        // $cmd = [
        //     $tippecanoeBin,
        //     '-o', $output,
        //     '-zg',
        //     '--drop-densest-as-needed',
        //     '--extend-zooms-if-still-dropping',
        //     '--no-tile-compression',
        //     '--simplification=20', // Aumentado para simplificar mais em datasets densos
        //     '--maximum-tile-bytes=500000', // Limita tamanho de tiles para evitar OOM
        //     '--read-parallel', // Processa large GeoJSON em paralelo
        //     '-z18', // Limita zoom máximo para evitar overprocessing
        //     '--force', // Força sobrescrita se o MBTiles já existir
        //     $geojsonPath // Use o GeoJSON em vez do shp
        // ];
        $cmd = [
            $tippecanoeBin,
            '-o', $output,
            '--minimum-zoom=0',  // Min zoom (ajuste para 2+ se dados locais)
            '--maximum-zoom=14', // Limite max para evitar sobrecarga (em vez de 18)
            '--drop-densest-as-needed',
            '--extend-zooms-if-still-dropping',
            '--no-tile-compression',
            '--simplification=20',
            '--maximum-tile-bytes=500000',
            '--read-parallel',
            '--force',
            $geojsonPath
        ];


        $process = new Process($cmd, null, null, null, $this->timeout);

        try {
            $process->mustRun(function ($type, $buffer) {
                Log::info($type === Process::OUT ? 'TIPPECANOE OUT: ' . $buffer : 'TIPPECANOE ERR: ' . $buffer);
            });

            // Sucesso
            $mbtilesSize = filesize($output) / 1024; // KB
            Log::info("MBTiles generated: {$output} (size: {$mbtilesSize} KB)");
            $this->layer->update([
                'status' => 'ready',
                'mbtiles_path' => "tiles/{$mbtilesName}",
                'error' => null
            ]);

        } catch (ProcessFailedException $e) {
            $err = "Tippecanoe failed. ExitCode: " . $process->getExitCode() . PHP_EOL .
                   "Cmd: " . implode(' ', $cmd) . PHP_EOL .
                   "Stdout: " . $process->getOutput() . PHP_EOL .
                   "Stderr: " . $process->getErrorOutput();

            Log::error($err);
            $this->layer->update([
                'status' => 'failed',
                'error' => $err
            ]);

            $this->cleanup($workDir);
            return;
        }

        // Opcional: ajustar permissões
        chmod($output, 0644); // Removido @ para expor erros
        $this->cleanup($workDir);
    }

    protected function failWith($message)
    {
        Log::error($message);
        $this->layer->update([
            'status' => 'failed',
            'error' => $message
        ]);
    }

    protected function cleanup($dir)
    {
        // Apaga recursivamente pasta temporária
        if (!is_dir($dir)) return;
        $it = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    protected function findShapefile($dir)
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($rii as $file) {
            if ($file->isFile()) {
                $name = $file->getFilename();
                if (preg_match('/\.shp$/i', $name)) {
                    Log::info("Found shp file: " . $file->getRealPath());
                    return $file->getRealPath();
                }
            }
        }
        return null;
    }
}
