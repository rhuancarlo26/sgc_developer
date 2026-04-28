<?php

namespace App\Helpers;

/**
 * Helper para gerar URLs públicas corretas de arquivos armazenados.
 */
class StorageHelper
{
    /**
     * Gera URL pública correcta para arquivo armazenado em disco público.
     * O Storage::url() pode não incluir APP_URL em alguns casos, então forcamos aqui.
     *
     * @param string $caminho Caminho relativo do arquivo (ex: anexos_fotos/espeleologia/1/1/nome.jpg)
     * @return string URL pública acessível (ex: /storage/anexos_fotos/espeleologia/1/1/nome.jpg)
     */
    public static function publicUrl($caminho)
    {
        if (!$caminho) {
            return null;
        }

        if (strpos($caminho, 'http') === 0) {
            return $caminho; // Já é URL absoluta
        }

        // Limpa o caminho
        $caminho = ltrim($caminho, '/');

        // Se já começa com /storage ou storage, retorna como está
        if (strpos($caminho, 'storage') === 0) {
            return '/' . $caminho;
        }

        // Prepend /storage/
        return '/storage/' . $caminho;
    }
}
