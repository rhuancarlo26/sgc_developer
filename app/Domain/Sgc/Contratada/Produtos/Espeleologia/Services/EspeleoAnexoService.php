<?php

namespace App\Domain\Sgc\Contratada\Produtos\Espeleologia\Services;

use App\Helpers\StorageHelper;
use App\Models\SgcEspeleoAnexo;
use App\Models\SgcEspeleoCampanha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EspeleoAnexoService
{
    public function uploadAnexo(Request $request, $contrato, $produto)
    {
        Log::info('Iniciando uploadAnexo', [
            'contrato' => $contrato,
            'produto' => $produto,
            'request_data' => $request->all(),
            'files' => $request->hasFile('fotos') ? array_map(fn($file) => $file->getClientOriginalName(), $request->file('fotos')) : [],
        ]);

        if ($produto !== 'espeleologia') {
            Log::error('Produto inválido', ['produto' => $produto]);
            throw new \Exception('Produto inválido', 404);
        }

        if (!$request->hasFile('fotos') || !$request->has('campanha_id')) {
            Log::error('Dados incompletos', ['request' => $request->all()]);
            throw new \Exception('Fotos ou campanha_id ausentes', 400);
        }

        $campanha = SgcEspeleoCampanha::find($request->campanha_id);
        if (!$campanha || $campanha->id_contrato != $contrato) {
            Log::error('Campanha inválida ou não pertence ao contrato', [
                'campanha_id' => $request->campanha_id,
                'contrato' => $contrato,
            ]);
            throw new \Exception('Campanha inválida ou não autorizada', 403);
        }

        $anexos = [];
        foreach ($request->file('fotos') as $foto) {
            if (!$foto->isValid() || !in_array($foto->getClientMimeType(), ['image/jpeg', 'image/png'])) {
                Log::warning('Arquivo inválido', ['nome' => $foto->getClientOriginalName()]);
                continue;
            }

            $nomeOriginal = $foto->getClientOriginalName();
            $nomeUnico = 'foto_' . $contrato . '_campanha_' . $campanha->id_campanha . '_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $caminhoPasta = 'anexos_fotos/espeleologia/' . $contrato . '/' . $campanha->id_campanha;
            $caminhoCompleto = $caminhoPasta . '/' . $nomeUnico;

            Storage::disk('public')->makeDirectory($caminhoPasta);
            $foto->storeAs($caminhoPasta, $nomeUnico, 'public');

            $anexo = SgcEspeleoAnexo::create([
                'id_contrato' => $contrato,
                'campanha_id' => $campanha->id,
                'tipo' => 'foto',
                'caminho' => $caminhoCompleto,
                'nome' => $nomeOriginal,
                'legenda' => '',
                'size' => $foto->getSize(),
            ]);

            Log::info('Anexo criado com sucesso', [
                'anexo_id' => $anexo->id,
                'campanha_id' => $campanha->id,
                'caminho' => $caminhoCompleto,
                'url_publica' => StorageHelper::publicUrl($caminhoCompleto),
            ]);

            $anexos[] = [
                'id' => $anexo->id,
                'nome' => $anexo->nome,
                'caminho' => StorageHelper::publicUrl($anexo->caminho),
                'tipo' => $anexo->tipo,
                'legenda' => $anexo->legenda,
                'url_publica' => StorageHelper::publicUrl($anexo->caminho),
                'size' => $anexo->size,
            ];
        }

        if (empty($anexos)) {
            Log::error('Nenhum anexo válido processado');
            throw new \Exception('Nenhum arquivo válido foi processado', 400);
        }

        Log::info('Imagens vinculadas com sucesso', [
            'contrato' => $contrato,
            'campanha_id' => $campanha->id,
            'anexos' => array_column($anexos, 'id'),
        ]);

        return $anexos;
    }

    public function updateAnexo(Request $request, $contrato, $produto, $id)
    {
        if ($produto !== 'espeleologia') {
            throw new \Exception('Produto inválido', 404);
        }

        $validated = $request->validate([
            'legenda' => 'nullable|string|max:5000',
        ]);

        $anexo = SgcEspeleoAnexo::findOrFail($id);
        if ($anexo->id_contrato != $contrato) {
            throw new \Exception('Acesso negado', 403);
        }

        $anexo->update(['legenda' => $validated['legenda']]);

        return true;
    }

    public function deleteAnexo($contrato, $produto, $id)
    {
        if ($produto !== 'espeleologia') {
            throw new \Exception('Produto inválido', 404);
        }

        $anexo = SgcEspeleoAnexo::findOrFail($id);
        if ($anexo->id_contrato != $contrato) {
            throw new \Exception('Acesso negado', 403);
        }

        Storage::disk('public')->delete($anexo->caminho);
        $anexo->delete();

        return true;
    }
}
