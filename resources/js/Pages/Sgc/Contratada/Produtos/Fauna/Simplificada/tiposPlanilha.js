export const tiposPlanilhaFauna = (subproduto) => {
    const descricao = String(subproduto ?? '').toLocaleLowerCase('pt-BR');
    const tipos = [
        { key: 'terrestre', label: 'Fauna Terrestre' },
        { key: 'aquatica', label: 'Fauna Aquática' },
        { key: 'cavernicola', label: 'Fauna Cavernícola' },
    ];
    return descricao.includes('atropelamento') ? tipos.slice(0, 1) : tipos;
};
