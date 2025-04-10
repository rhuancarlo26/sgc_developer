import { useForm } from '@inertiajs/vue3';

export const toggleAprovado = async (item, contratoId, relatorioNum, novoAprovado) => {
    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!token) throw new Error('CSRF token não encontrado');

        const response = await fetch(route('sgc.relatorio_coordenacao.toggle_aprovado', { id: item.id_item }), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                aprovado: novoAprovado, // Usa o valor passado (0 ou 1)
                contrato_id: contratoId,
                relatorio_num: relatorioNum
            })
        });

        if (!response.ok) throw new Error('Erro na requisição');

        const result = await response.json();
        if (result.success) {
            item.aprovado = result.aprovado; // Atualiza localmente
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Erro ao atualizar:', error);
    }
};