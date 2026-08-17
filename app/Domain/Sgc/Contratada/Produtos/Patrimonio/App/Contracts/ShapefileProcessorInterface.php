<?php
namespace App\Domain\Sgc\Contratada\Produtos\Patrimonio\App\Contracts;

use Illuminate\Http\UploadedFile;

interface ShapefileProcessorInterface
{
  public function process(UploadedFile $filePath): string;
}
