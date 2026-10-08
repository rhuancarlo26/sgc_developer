export const classesAnexos = [
    { id: 'abio', titulo: 'ABIO' },
    { id: 'anuencia_proprietarios', titulo: 'Anuência dos Proprietários' },
    { id: 'registro_fotografico', titulo: 'Registro Fotográfico' },
    { id: 'dados_secundarios', titulo: 'Dados Secundários' },
    { id: 'art', titulo: 'ARTs' },
    { id: 'ret_cr_ctf', titulo: 'RET, CR e CTF' },
    { id: 'anuencia_colecoes', titulo: 'Anuência Coleções' },
    { id: 'oficios', titulo: 'Ofícios' },
];

export function classeAnexo(anexo) {
    return anexo.classe || anexo.metadados?.classe || 'outros';
}

export function tituloAnexo(anexo) {
    const classe = classeAnexo(anexo);
    return classesAnexos.find(item => item.id === classe)?.titulo
        || anexo.titulo_bloco || anexo.metadados?.titulo_bloco || 'Outros';
}

export function agruparAnexos(anexos = []) {
    const grupos = classesAnexos.map(item => ({ ...item, arquivos: [] }));
    for (const anexo of anexos) {
        const id = classeAnexo(anexo);
        let grupo = grupos.find(item => item.id === id);
        if (!grupo) {
            grupo = { id, titulo: tituloAnexo(anexo), arquivos: [] };
            grupos.push(grupo);
        }
        if (!anexo.remover) grupo.arquivos.push(anexo);
    }
    return grupos;
}
