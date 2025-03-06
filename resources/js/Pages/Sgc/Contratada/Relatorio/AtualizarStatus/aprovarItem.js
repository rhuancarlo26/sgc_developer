
export const toggleAprovado = async (item, contratoId, relatorioNum) => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.toggle_aprovado', { id: item.id_item }), {
      method: 'POST',
      headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ 
          aprovado: item.aprovado ? 0 : 1,
          contrato_id: contratoId,
          relatorio_num: relatorioNum
      })
    });

    if (!response.ok) {
        throw new Error(`Erro ao atualizar status: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('Status aprovado atualizado:', result);

    item.aprovado = item.aprovado ? 0 : 1;
  } catch (error) {
      console.error('Erro ao atualizar status:', error);
  }
};
