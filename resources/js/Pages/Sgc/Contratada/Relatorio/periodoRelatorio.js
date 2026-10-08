const lerData = (dia, mes, ano) => {
    const data = new Date(Date.UTC(Number(ano), Number(mes) - 1, Number(dia)));
    return data.getUTCFullYear() === Number(ano) && data.getUTCMonth() === Number(mes) - 1 && data.getUTCDate() === Number(dia) ? data : null;
};

export const sugerirPeriodoRelatorio = (relatorios = []) => {
    const ordenados = [...relatorios].sort((a, b) => Number(b.relatorio_num) - Number(a.relatorio_num));
    for (const relatorio of ordenados) {
        const datas = String(relatorio.periodo || '').match(/^(\d{2})\/(\d{2})\/(\d{4})\s+(?:a|à)\s+(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!datas) continue;
        const inicio = lerData(datas[1], datas[2], datas[3]);
        const fim = lerData(datas[4], datas[5], datas[6]);
        if (!inicio || !fim || fim < inicio || fim.toISOString().slice(0, 10) === '2000-01-01') continue;
        const proximoInicio = new Date(fim);
        proximoInicio.setUTCDate(proximoInicio.getUTCDate() + 1);
        // Dois meses de calendário, sem depender do fuso do navegador.
        const proximoFim = new Date(Date.UTC(proximoInicio.getUTCFullYear(), proximoInicio.getUTCMonth() + 2, 0));
        return { data_inicio: proximoInicio.toISOString().slice(0, 10), data_fim: proximoFim.toISOString().slice(0, 10) };
    }
    return { data_inicio: '', data_fim: '' };
};
