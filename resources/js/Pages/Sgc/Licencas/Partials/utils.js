/**
 * Formata 'YYYY-MM-DD' em 'DD/MM/YYYY' sem conversão de fuso.
 */
export const formatarData = (data) => {
    if (!data) {
        return '';
    }
    const [ano, mes, dia] = String(data).substring(0, 10).split('-');
    return `${dia}/${mes}/${ano}`;
};

export const hoje = () => new Date().toISOString().substring(0, 10);

export const MESES = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

/**
 * Texto curto para "faltam X dias" / "vencido há X dias".
 */
export const textoDias = (dias) => {
    if (dias === null || dias === undefined) {
        return '';
    }
    if (dias < 0) {
        return `vencido há ${Math.abs(dias)} dia(s)`;
    }
    if (dias === 0) {
        return 'vence hoje';
    }
    return `faltam ${dias} dia(s)`;
};

/**
 * Descrição legível da regra de prazo de uma condicionante.
 */
export const descreverRegra = (c) => {
    switch (c.tipo_prazo) {
        case 'dias_apos_emissao':
            return `${c.prazo_dias} dias após a emissão`;
        case 'dias_antes_vencimento':
            return `${c.prazo_dias} dias antes do vencimento`;
        case 'data_fixa':
            return `Até ${formatarData(c.data_fixa)}`;
        case 'anual':
            return `Anual, até ${String(c.dia_recorrencia).padStart(2, '0')}/${String(c.mes_recorrencia).padStart(2, '0')}`
                + (c.periodo_ano_anterior ? ' (ref. ano anterior)' : '');
        case 'apos_evento':
            return `${c.prazo_dias} dias após evento`;
        default:
            return 'Sem prazo';
    }
};
