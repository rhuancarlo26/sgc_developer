<?php

namespace App\Shared\Utils;

use Illuminate\Support\Facades\Response;

class ArquivoUtils
{
    public function visualizar($caminho): \Illuminate\Http\Response
    {
        return Response::make(
            content: file_get_contents(storage_path('app') . DIRECTORY_SEPARATOR . $caminho),
            headers: ["Content-type" => "application/pdf"]
        );
    }
}