import { usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import Swal from 'sweetalert2';

export function usePmqaPermissions(props, fase = null) {
    const page = usePage();

    const isVisualizacaoMode = computed(() => page.url.includes('visualizar=true'));

    const getStatus = () => {
        if (!props.pmqa) return null;
        if (!fase || fase === 'pmqa') return props.pmqa.status_aprovacao;
        return props.pmqa[`status_${fase}`] || null;
    };

    // Define se a contratada pode criar/editar itens (botões de Novo, Editar, Excluir, Lápis)
    const podeEditarFase = computed(() => {
        if (isVisualizacaoMode.value) return false;
        if (props.canApprove) return false; // Fiscal nunca edita, apenas aprova/reprova

        const status = getStatus();
        return status === 'Em elaboração' || status === 'Reprovada' || status === 'Bloqueado' || !status;
    });

    // Define se o fiscal pode aprovar/reprovar a fase atual
    const podeAprovarFase = computed(() => {
        if (isVisualizacaoMode.value) return false;
        if (!props.canApprove) return false; // Contratada não aprova

        const status = getStatus();
        return status === 'Em análise';
    });

    // Mantido por compatibilidade temporária com os botões que ainda usam
    const podeGerenciar = computed(() => podeEditarFase.value || podeAprovarFase.value);

    // Define se os campos dos formulários internos (inputs, selects) ficarão desabilitados
    const isReadonlyForm = computed(() => {
        if (isVisualizacaoMode.value) return true;
        if (props.canApprove) return true; // Fiscal nunca edita os dados

        const status = getStatus();
        return status !== 'Em elaboração' && status !== 'Reprovada' && status !== null;
    });

    const reprovarFase = async () => {
        if (!props.pmqa) return;
        
        const { value: motivo } = await Swal.fire({
            title: 'Reprovar',
            input: 'textarea',
            inputLabel: 'Insira as solicitações de correções ou motivo da reprovação:',
            inputPlaceholder: 'Digite aqui os ajustes necessários...',
            showCancelButton: true,
            confirmButtonText: '✖ Reprovar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Você precisa inserir o motivo ou solicitação de correção!'
                }
            }
        });

        if (motivo) {
            const contratoId = props.contratos?.id ?? props.contrato?.id ?? props.contratos ?? props.contrato ?? route().params.contrato;
            const produtoId = props.produto?.slug ?? props.produto ?? route().params.produto;

            router.patch(route('sgc.contratada.produtos.pmqa.reprovar', [
                contratoId,
                produtoId,
                props.pmqa.id
            ]), {
                motivo: motivo
            }, { preserveScroll: true });
        }
    };

    return {
        isVisualizacaoMode,
        podeGerenciar,
        isReadonlyForm,
        reprovarFase
    };
}
