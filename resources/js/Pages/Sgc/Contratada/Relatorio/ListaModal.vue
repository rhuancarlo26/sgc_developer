<script setup>
import Modal from '@/Components/Modal.vue';
import Tabs from './Tabs.vue';
import { onMounted, ref } from "vue";

const modalDetalhes = ref(null);
const emp = ref([]);
const empcontratos = ref([]);
const empproduto = ref([]);
const versao_00 = ref([]);
const versao_01 = ref([]);
const versao_02 = ref([]);
const versao_03 = ref([]);
const versao_04 = ref([]);
const versao_05 = ref([]);
const entrega = ref([]);
// Definições de grupos de campos do empreendimento:
const datasContrato = [
    'br',
    'uf',
    'km_inicial',
    'km_final',
    'tipo_de_intervencao',
    'competencia_do_licenciamento',
    'ose_emitida_sei',
    'ose_emitida_data',
    'item_edital',
    'cod_siac'
];
const datasProduto = [
    'produto',
    'subproduto',
    'quantidade_ose',
    'valores_ose',
    'data_prevista_de_entrega',
    'situacao_entregue_aprovado_medido',
    'medicao_caso_medido',
    'processo_medicao',
]
const datasEntrega = [
    'avanco',
    'abio_sei',
    'abio_data',
    'lp_sei',
    'lp_data',
    'li_sei',
    'li_data',
    'alerta_ponto_critico'
]

const formatDate = (key, timestamp) => {
    // Verifica se o valor é um número antes de tentar formatar
    return key.includes('data_') ? Date(timestamp,'d/m/Y') : timestamp;
}

const abrirModal = async (iPosts) => {
    modalDetalhes.value.getBsModal().show();
    // console.log(iPosts);
    emp.value = iPosts;

    const groupedItems = ref({
        contrato: [],
        produto: [],
        versao_00: [],
        versao_01: [],
        versao_02: [],
        versao_03: [],
        versao_04: [],
        versao_05: [],
        entrega: []
    });

    if (iPosts && typeof iPosts === 'object') {
        // Group items
        Object.entries(iPosts).forEach(([key, value]) => {
            if (datasContrato.some(substring => key.includes(substring))) {
                groupedItems.value.contrato.push({ key, value: formatDate(key, value) });
            }
            if (datasProduto.some(substring => key.includes(substring))) {
                groupedItems.value.produto.push({ key, value: formatDate(key, value) });
            }
            if (key.includes('versao_00')) { groupedItems.value.versao_00.push({ key, value: formatDate(key, value) }); }
            if (key.includes('versao_01')) { groupedItems.value.versao_01.push({ key, value: formatDate(key, value) }); }
            if (key.includes('versao_02')) { groupedItems.value.versao_02.push({ key, value: formatDate(key, value) }); }
            if (key.includes('versao_03')) { groupedItems.value.versao_03.push({ key, value: formatDate(key, value) }); }
            if (key.includes('versao_04')) { groupedItems.value.versao_04.push({ key, value: formatDate(key, value) }); }
            if (key.includes('versao_05')) { groupedItems.value.versao_05.push({ key, value: formatDate(key, value) }); }
            if (datasEntrega.some(substring => key.includes(substring))) {
                groupedItems.value.entrega.push({ key, value: formatDate(key, value) });
            }
        });
    } else {
        console.error('iPosts is not an object:', iPosts);
    }

    // console.log(groupedItems.value.contrato);
    empcontratos.value = groupedItems.value.contrato;
    empproduto.value = groupedItems.value.produto;
    versao_00.value = groupedItems.value.versao_00;
    versao_01.value = groupedItems.value.versao_01;
    versao_02.value = groupedItems.value.versao_02;
    versao_03.value = groupedItems.value.versao_03;
    versao_04.value = groupedItems.value.versao_04;
    versao_05.value = groupedItems.value.versao_05;
    entrega.value = groupedItems.value.entrega;

}

defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalDetalhes" modal-dialog-class="modal-xl">
        <template #body>
            <div class="card">
                <div class="card-header">
                    <h3 class="my-0">EMPREENDIMENTO: {{ emp.empreendimento }}</h3>
                </div>
                <div class="card-body">
                    <br>
                    Contrato: <strong>{{ emp.contrato }}</strong><br>
                    Empresa: <strong>{{ emp.empresa }}</strong><br>
                    <br/>
                    <br/>
                    <Tabs :tabs="[
                        { label: 'Empreendimento:', name: 'tab1' },
                        { label: 'Produto', name: 'tab2' },
                        { label: 'V.: 00 => ', name: 'tab3' },
                        { label: 'V.: 01 => ', name: 'tab4' },
                        { label: 'V.: 02 => ', name: 'tab5' },
                        { label: 'V.: 03 => ', name: 'tab6' },
                        { label: 'V.: 04 => ', name: 'tab7' },
                        { label: 'V.: 05 => ', name: 'tab8' },
                        { label: 'Entrega', name: 'tab9' }
                    ]">
                        <template #tab1>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in empcontratos" :key="id">
                                    <label>{{ item.key }}: </label> <strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab2>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in empproduto" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab3>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_00" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab4>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_01" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab5>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_02" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab6>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_03" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab7>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_04" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab8>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in versao_05" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                        <template #tab9>
                            <ul class="lista-itens">
                                <li v-for="(item, id) in entrega" :key="id">
                                    <label>{{ item.key }}: </label><strong>{{ item.value }}</strong>
                                </li>
                            </ul>
                        </template>
                    </Tabs>
                </div>
            </div>
        </template>
    </Modal>
</template>
<style>
    .lista-itens label {
        font-size: 20px !important;
        margin: 5px 1px;
        text-transform: capitalize;
        color: gray;

    }.lista-itens strong {
        font-size: 20px !important;
        margin: 5px 10px;
        /* font-weight: 200px; */
        text-transform: capitalize;
    }
</style>
