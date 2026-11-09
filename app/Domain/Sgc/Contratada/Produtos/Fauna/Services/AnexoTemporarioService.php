<?php

namespace App\Domain\Sgc\Contratada\Produtos\Fauna\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AnexoTemporarioService
{
    public function receber(UploadedFile $arquivo, int $contrato, int $usuario): string
    {
        $diretorio = "fauna_anexos_temporarios/{$usuario}/{$contrato}";
        // Limpa uploads abandonados do próprio usuário, sem depender de uma tarefa no servidor.
        foreach (Storage::disk('local')->files($diretorio) as $antigo) {
            if (Storage::disk('local')->lastModified($antigo) < now()->subDay()->timestamp) {
                Storage::disk('local')->delete($antigo);
            }
        }
        $caminho = $arquivo->store($diretorio, 'local');
        if (!$caminho) throw ValidationException::withMessages(['arquivo' => 'Não foi possível armazenar o anexo. Tente novamente.']);
        return Crypt::encryptString(json_encode([
            'caminho' => $caminho, 'contrato' => $contrato, 'usuario' => $usuario,
            'nome' => $arquivo->getClientOriginalName(), 'mime' => $arquivo->getMimeType(),
            'tamanho' => $arquivo->getSize(), 'expira' => now()->addDay()->timestamp,
        ]));
    }

    public function consultar(string $token, int $contrato, int $usuario): array
    {
        try {
            $dados = json_decode(Crypt::decryptString($token), true, flags: JSON_THROW_ON_ERROR);
            if ($dados['contrato'] !== $contrato || $dados['usuario'] !== $usuario
                || $dados['expira'] < now()->timestamp
                || !Storage::disk('local')->exists($dados['caminho'])) {
                throw new \RuntimeException();
            }
            return $dados;
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(['anexos' => 'Um upload expirou ou não está disponível. Tente salvar novamente para reenviar os anexos.']);
        }
    }
}
