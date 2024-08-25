<script setup>
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    pilhas: {type: Array},
    servico: {type: Object},
    resultados: {type: Array},
    relatorios: {type: Array},
})

const form = useForm({
    id: null,
    nome_relatorio: null,
    fk_resultado: null,
    sobre_relatorio: null,
})

const modalRef = ref();
const abrirModal = (item) => {
    form.reset()
    if (item != null) {
        Object.assign(form, item);
    }
    getResultadosToFilter(props.resultados, item?.fk_resultado);
    modalRef.value.getBsModal().show();
}
const save = () => {
    form.transform((data) => ({
        ...data,
        fk_servico: props.servico.id,
    }));

    const onSuccess = () => {
        modalRef.value.getBsModal().hide();
        form.reset();
    }

    if (form.id !== null) {
        router.post(route('contratos.contratada.servicos.supressao-vegetacao.execucao.destinacao.update'), {
            _method: 'patch',
            ...form.data(),
            pilhas: form.pilhas.map(pilha => pilha?.id),
            arquivos: form.arquivos.filter(foto => foto instanceof File),
        }, {
            preserveState: true,
            onSuccess
        })
        return
    }

    form.post(route('contratos.contratada.servicos.supressao-vegetacao.relatorio.store'), {
        preserveState: true,
        onSuccess
    })
}

const resultadosFiltrados = ref([]);

const getResultadosToFilter = (resultados, fk_resultado = null) => {
    resultadosFiltrados.value = [];

    if (props.relatorios.length > 0) {
        resultados.forEach(result => {
            props.relatorios.forEach(relat => {
                if (result.id == relat.fk_resultado)
                    resultadosFiltrados.value.push(relat.fk_resultado)
            })
        })

        resultadosFiltrados.value = resultadosFiltrados.value.filter((ele, index) => { // filtrar possíveis resultados duplicados
            return resultadosFiltrados.value.indexOf(ele) == index;
        })

        if (fk_resultado) {
            resultadosFiltrados.value = resultadosFiltrados.value.filter((resultado, index) => {
                return resultado !== fk_resultado;
            });
        }
    }
};

const filterResultados = (id_resultado) => {
    return !resultadosFiltrados.value.includes(id_resultado)
}

const getNomePeriodo = (periodo) => {
    switch (periodo) {
        case 'M':
            return 'Mensal';
        case 'S':
            return 'Semestral';
        case 'A':
            return 'Anual';
        case 'P':
            return 'Período';
        default:
            return '';
    }
}

defineExpose({abrirModal});
</script>

<template>
    <form @submit.prevent="save">
        <Modal ref="modalRef" title="Cadastro de Destinação de Pilhas" modal-dialog-class="modal-xl">
            <template #body>
                <div class="row row-gap-2 mb-2">
                    <div class="col-lg-8">
                        <InputLabel value="Nome do relatório" for="nome_relatorio"/>
                        <input v-model="form.nome_relatorio" id="nome_relatorio" class="form-control" />
                        <InputError :message="form.errors.nome_relatorio"/>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2 align-items-end">
                    <InputLabel value="Selecionar resultado" for="lista_campanhas"/>
                    <div class="col-12">
                        <Table
                            :columns="[' ', 'Nome do Resultado', 'Período de Análise', 'Data Início', 'Data Final']"
                            :records="{ data: resultados, links: [] }"
                            table-class="table-hover">
                            <template #body="{ item, key }">
                                <tr v-if="filterResultados(item.id)">
                                    <td>
                                        <input type="radio" :id="item.id" :value="item.id" v-model="form.fk_resultado">
                                    </td>
                                    <td>
                                        <label :for="item.id">{{item.chave}}</label>
                                    </td>
                                    <td>{{getNomePeriodo(item.periodo)}}</td>
                                    <td>{{item.dt_inicio}}</td>
                                    <td>{{item.dt_final}}</td>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
                <div class="row row-gap-2 mb-2">
                    <div class="col-12">
                        <InputLabel value="Sobre o Relatório" for="sobre_relatorio"/>
                        <textarea v-model="form.sobre_relatorio" id="sobre_relatorio" class="form-control"></textarea>
                        <InputError :message="form.errors.sobre_relatorio"/>
                    </div>
                </div>
            </template>
            <template #footer>
                <button @click="modalRef.getBsModal().hide()" type="button" class="btn btn-secondary">Fechar</button>
                <button type="submit" class="btn btn-success">Salvar</button>
            </template>
        </Modal>
    </form>
</template>
