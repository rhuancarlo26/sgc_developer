<?php

namespace App\Domain\Servico\ContOcorrencia\Configuracao\LoteObra\Controller;

use App\Shared\Http\Controllers\Controller;
use GuzzleHttp\Client;

class GetContratoDnitController extends Controller
{
  public function getContrato(String $nr_contrato)
  {
    $client = new Client();

    $url = "https://servicos.dnit.gov.br/DPP/api/contrato/dnit/{$nr_contrato}";

    try {
      $response = $client->request('GET', $url, [
        'headers' => [
          'Accept' => 'application/json',
        ],
      ]);

      $statusCode = $response->getStatusCode();
      if ($statusCode == 200) {
        $responseData = $response->getBody()->getContents();
        return response()->json(json_decode($responseData));
      } else {
        return response()->json(['error' => 'Error fetching data from API'], $statusCode);
      }
    } catch (\Exception $e) {
      return response()->json(['error' => 'Exception caught: ' . $e->getMessage()], 500);
    }
  }
}