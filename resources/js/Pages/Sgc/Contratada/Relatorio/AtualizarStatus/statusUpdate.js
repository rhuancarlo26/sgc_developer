
export const atualizarStatusLocal = (itemId, novoStatus, itens) => {
  const item = itens.find(i => i.id_item === itemId);
  if (item) {
    item.status = novoStatus;
  }
};

export const atualizarStatus = async (contratoId, itemId, relatorioNum, itens) => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.update_status'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ contrato_id: contratoId, relatorio_num: relatorioNum, status: 'Análise DNIT', id_item: itemId })
    });

    if (!response.ok) {
      throw new Error(`Erro ao atualizar status: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('Status atualizado:', result);
    atualizarStatusLocal(itemId, 'Análise DNIT', itens);

  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};

export const revisaoStatus = async (contratoId, itemId, relatorioNum, itens) => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.revisao_status'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ contrato_id: contratoId, relatorio_num: relatorioNum, status: 'Revisão Contratada', id_item: itemId })
    });

    if (!response.ok) {
      throw new Error(`Erro ao atualizar status: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('Status atualizado:', result);
    atualizarStatusLocal(itemId, 'Revisão Contratada', itens);

  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};

export const aprovadoStatus = async (contratoId, itemId, relatorioNum, itens) => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.aprovado_status'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ contrato_id: contratoId, relatorio_num: relatorioNum, status: 'Relatório Aprovado', id_item: itemId })
    });

    if (!response.ok) {
      throw new Error(`Erro ao atualizar status: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('Status atualizado:', result);
    atualizarStatusLocal(itemId, 'Relatório Aprovado', itens);

  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};
