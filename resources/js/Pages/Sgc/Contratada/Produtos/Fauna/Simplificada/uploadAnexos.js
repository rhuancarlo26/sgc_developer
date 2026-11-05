export async function enviarAnexosPendentes(anexos, enviar) {
    for (const anexo of anexos) {
        if (anexo.id || anexo.remover || anexo.upload_token) continue;
        anexo.upload_status = 'enviando';
        anexo.upload_progresso = 0;
        anexo.upload_erro = '';
        try {
            const token = await enviar(anexo.arquivo, progresso => { anexo.upload_progresso = progresso; });
            anexo.upload_token = token;
            anexo.upload_status = 'recebido';
            anexo.upload_progresso = 100;
        } catch (error) {
            anexo.upload_status = 'erro';
            anexo.upload_erro = error.response?.status === 413
                ? 'O servidor recusou o tamanho deste arquivo. Reduza o arquivo e tente novamente.'
                : error.response?.data?.errors?.arquivo?.[0] || 'Falha ao enviar o arquivo. Clique em salvar novamente para tentar outra vez.';
            throw error;
        }
    }
}

export function referenciasAnexos(anexos) {
    return anexos.map(({ id, upload_token, classe, titulo_bloco, remover }) => ({ id, upload_token, classe, titulo_bloco, remover }));
}
