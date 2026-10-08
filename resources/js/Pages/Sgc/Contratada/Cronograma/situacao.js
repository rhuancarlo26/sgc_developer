export const situacoes = {
  atrasada: { label: 'Atrasada', cor: '#b91c1c', descricao: 'Prazo vencido, sem entrega registrada.' },
  no_prazo: { label: 'Entrega no Prazo', cor: '#15803d', descricao: 'Entrega registrada até o prazo previsto.' },
  entrega_atrasada: { label: 'Entrega Atrasada', cor: '#f97316', descricao: 'Entrega registrada após o prazo previsto.' },
  prevista: { label: 'Prevista', cor: '#1d4ed8', descricao: 'Prazo hoje ou futuro, sem entrega registrada.' },
  entregue: { label: 'Entregue (sem prazo)', cor: '#7e22ce', descricao: 'Entrega registrada, sem prazo válido para comparação.' },
  sem_previsao: { label: 'Sem previsão', cor: '#64748b', descricao: 'Sem entrega registrada e sem prazo válido.' },
};

// Datas de calendário são comparadas sem a conversão UTC de YYYY-MM-DD.
function dataCalendario(valor) {
  if (typeof valor !== 'string') return null;
  const partes = /^(\d{4})-(\d{2})-(\d{2})(?:$|[T ])/.exec(valor.trim());
  if (!partes) return null;
  const [, ano, mes, dia] = partes.map(Number);
  const data = new Date(ano, mes - 1, dia);
  return data.getFullYear() === ano && data.getMonth() === mes - 1 && data.getDate() === dia
    ? data : null;
}

export function obterSituacao(evento, hoje = new Date()) {
  const prazo = dataCalendario(evento.end);
  // A primeira entrega é a referência; a versão aceita comprova entrega quando a 00 não existe.
  const entrega = dataCalendario(evento.versao_00_data_de_entrega)
    || dataCalendario(evento.versao_aceita_data);
  if (entrega) {
    if (!prazo) return 'entregue';
    return entrega <= prazo ? 'no_prazo' : 'entrega_atrasada';
  }
  if (!prazo) return 'sem_previsao';
  const inicioHoje = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
  return prazo < inicioHoje ? 'atrasada' : 'prevista';
}

export function formatarData(valor) {
  const data = dataCalendario(valor);
  return data ? data.toLocaleDateString('pt-BR') : 'N/A';
}

export function fimExclusivo(valor) {
  const data = dataCalendario(valor);
  if (!data) return null;
  data.setDate(data.getDate() + 1);
  return `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}-${String(data.getDate()).padStart(2, '0')}`;
}
