export const dataParaInput = valor => String(valor ?? '').slice(0, 10);
export const formatarDataCampanha = valor => {
    const data = dataParaInput(valor);
    return /^\d{4}-\d{2}-\d{2}$/.test(data) ? data.split('-').reverse().join('/') : 'N/A';
};
