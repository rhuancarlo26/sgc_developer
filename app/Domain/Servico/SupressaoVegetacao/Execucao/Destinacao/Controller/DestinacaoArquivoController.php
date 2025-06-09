<?php

namespace App\Domain\Servico\SupressaoVegetacao\Execucao\Destinacao\Controller;

use App\Models\Arquivo;
use App\Models\Destinacao;
use App\Shared\Http\Controllers\Controller;
use App\Shared\Utils\ArquivoUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinacaoArquivoController extends Controller
{
    public function listar(Destinacao $destinacao)
    {
        return response()->json(
            $destinacao->arquivos->map(function ($arquivo) {
                return [
                    'id' => $arquivo->id,
                    'nome_arquivo' => $arquivo->nome_arquivo,
                    'caminho' => asset($arquivo->diretorio . $arquivo->arquivo),
                ];
            })
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'arquivo' => 'required|file',
            'destinacao_id' => 'required|exists:destinacaos,id',
        ]);

        try {
            $destinacao = Destinacao::find($request->destinacao_id);

            $arquivoUtils = new ArquivoUtils();

            $fotos = $arquivoUtils->handleFotos(
                fotos: [$request->file('arquivo')],
                diretorio: 'public/uploads/supressao/destinacaos/',
                prefixo: 'DP'
            );

            if (empty($fotos)) {
                throw new \Exception('Nenhum ID de foto retornado do handleFotos.');
            }

            $destinacao->arquivos()->attach($fotos);

            $arquivo = \App\Models\Arquivo::latest()->first();

            return response()->json([
                'id' => $arquivo->id,
                'nome_arquivo' => $arquivo->nome_arquivo,
                'caminho' => asset($arquivo->diretorio . $arquivo->arquivo),
            ]);
        } catch (\Throwable $e) {
            [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];

            return response()->json(['error' => 'Erro interno ao salvar imagem'], 500);
        }
    }

    public function destroy(Destinacao $destinacao, Arquivo $arquivo)
    {
        $destinacao->arquivos()->detach($arquivo->id);
        Storage::delete('public/' . $arquivo->diretorio . $arquivo->arquivo);
        $arquivo->delete();

        return response()->json(['success' => true]);
    }
}
