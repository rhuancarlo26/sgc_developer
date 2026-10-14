export function formatarDataRelatorio(valor, ausente = 'Não informado') {
  if (!valor) return ausente;
  const data = new Date(valor);
  if (Number.isNaN(data.getTime())) return ausente;
  return new Intl.DateTimeFormat('pt-BR', {
    timeZone: 'America/Sao_Paulo', day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
  }).format(data);
}
