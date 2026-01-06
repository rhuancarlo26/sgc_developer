<script setup>
import { ref, watch } from "vue";
import Modal from "@/Components/Modal.vue";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import { useToast } from "vue-toastification";

const props = defineProps({
    subprodutos: Object,
    produtos: Object,
    empreendimentos: Object,
    contrato: Object,
    profissionais: Object,
    consumos: Object,
});

const toast = useToast();
const produtoSelecionado = ref("");
const modalVisualizarForm = ref(null);

const subprodutosFiltrados = ref([]);
const origemSeiSelecionada = ref("");
const modalVisualizarFormCadFunc = ref();

const mostrarCadastroProfissional = ref(false);

const novoProfissional = ref({
    nome: "",
    formacao: "",
});

const formDav = useForm({
    contrato_id: props.contrato.id,
    seq_dav: "",
    empreendimento: "",
    coordenador: "",
    finalidade: "",
    escopo: "",
    dataInicio: "",
    dataFinal: "",
    produto: "",
    subproduto: "",
    profissionais: [
        {
            profissional: null,
            origem: "",
            destino: "",
            dataInicio: "",
            dataFinal: "",
        },
    ],
    transporte: [],
    aereo_valor: 0,
    terrestre_tipo: [],
    terrestre_valor: 0,
    aquatico_valor: 0,
    status: "pendente",
    origem_sei: "",
});

const opcoesProfissionais = ref([]);

props.profissionais.forEach((profissional) => {
    opcoesProfissionais.value.push(profissional);
});

const adicionarProfissional = () => {
    formDav.profissionais.push({
        profissional: null,
        origem: "",
        destino: "",
        dataInicio: "",
        dataFinal: "",
    });
};

const removerProfissional = (index) => {
    if (formDav.profissionais.length > 1) {
        formDav.profissionais.splice(index, 1);
    }
};

const filterProdutos = () => {
    const produtos = [];

    const listaExclusao = ["2", "9", "10", "11", "12", "14"];

    for (const produto of props.produtos) {
        if (!listaExclusao.includes(produto.produto)) {
            const descricaoCompleta = `${produto.produto}.${produto.descricao_produto}`;
            produtos.push(descricaoCompleta);
        }
    }

    return produtos;
};

const getSequencialDav = () => {
    const key = "dav_sequencial";
    let atual = Number(localStorage.getItem(key) || 0);
    atual += 1;
    localStorage.setItem(key, atual);
    return atual;
};

const formatarEmpreendimento = (emp) => {
    if (!emp) return "";
    return emp.split("-")[0].trim();
};

const abrirCadastroProfissionais = () => {
    modalVisualizarFormCadFunc.value?.abrirModal();
};

const adicionarDestinos = () => {
    if (origem.value.length === destino.value.length) {
        origem.value.push(origem.value.length + 1);
        formDav.origem.push("");
    } else {
        destino.value.push(destino.value.length + 1);
        formDav.destino.push("");
    }
};

const calcularConsumo = (diaria, aerea, terrestreTipo, carro, barco) => {
    const numeroFormatado = props.contrato.numero_contrato.replace(
        /^0+\s*|(?<=\s)0+/g,
        ""
    );

    const consumosFiltrados = props.consumos.filter(
        (consumo) => consumo.contrato === numeroFormatado
    );

    const totalConsumo = consumosFiltrados.reduce(
        (acc, consumo) => {
            const novoAcc = {
                diarias: acc.diarias + (consumo.diarias || 0),
                aerea: acc.aerea + (consumo.aerea || 0),
                barco: acc.barco + (consumo.barco || 0),
                pickup: acc.pickup + (consumo.pickup || 0),
                hatch: acc.hatch + (consumo.hatch || 0),
            };

            for (const tipo of terrestreTipo) {
                if (tipo === "Pick-up") {
                    novoAcc.pickup -= carro || 0;
                } else if (tipo === "Hatch") {
                    novoAcc.hatch -= carro || 0;
                }
            }

            return novoAcc;
        },
        { diarias: 0, aerea: 0, barco: 0, pickup: 0, hatch: 0 }
    );

    return {
        diarias: totalConsumo.diarias - diaria,
        aerea: totalConsumo.aerea - aerea,
        barco: totalConsumo.barco - barco,
        pickup: totalConsumo.pickup - (terrestreTipo === "Pick-up" ? carro : 0), // Subtrai carro se for Pick-up
        hatch: totalConsumo.hatch - (terrestreTipo === "Hatch" ? carro : 0), // Subtrai carro se for Hatch
    };
};

const abrirModal = () => {
    modalVisualizarForm.value.getBsModal().show();
};

const handleTransporteChange = () => {
    if (!formDav.transporte.includes("Aéreo")) {
        formDav.aereo_valor = 0;
    }
    if (!formDav.transporte.includes("Terrestre")) {
        formDav.terrestre_tipo = [];
        formDav.terrestre_valor = 0;
    }
    if (!formDav.transporte.includes("Aquático")) {
        formDav.aquatico_valor = 0;
    }
};

const handleTerrestreTipoChange = () => {
    if (formDav.terrestre_tipo.length === 0) {
        formDav.terrestre_valor = 0;
    }
};

const salvarNovoProfissional = async () => {
    if (!novoProfissional.value.nome || !novoProfissional.value.formacao) {
        toast.error("Preencha nome e formação");
        return;
    }

    try {
        const { data } = await axios.post(
            route("sgc.gestao.storeDavProfissionais"),
            {
                profissionais: [
                    {
                        nome: novoProfissional.value.nome,
                        formacao: novoProfissional.value.formacao,
                        contrato_id: props.contrato.id,
                    },
                ],
            }
        );

        const profissional = data.profissional.model;

        opcoesProfissionais.value.push(profissional);

        const ultimoIndex = formDav.profissionais.length - 1;
        formDav.profissionais[ultimoIndex].profissional = {
            id: profissional.id,
            nome: profissional.nome,
            formacao: profissional.formacao,
        };

        toast.success("Profissional cadastrado com sucesso");

        novoProfissional.value = { nome: "", formacao: "" };
        mostrarCadastroProfissional.value = false;
    } catch (error) {
        toast.error(
            error.response?.data?.message || "Erro ao cadastrar profissional"
        );
    }
};

watch(produtoSelecionado, (novoValor) => {
    formDav.produto = novoValor;
    if (!novoValor) {
        subprodutosFiltrados.value = [];
        return;
    }

    const numeroProduto = novoValor.split(".")[0];

    subprodutosFiltrados.value = props.subprodutos
        .filter((subProduto) =>
            subProduto.subproduto.startsWith(numeroProduto + ".")
        )
        .map((subProduto) => ({
            value: subProduto.subproduto,
            label: `${subProduto.subproduto} ${subProduto.descricao_revisada}`,
        }));
});

watch(() => formDav.transporte, handleTransporteChange, { deep: true });

watch(() => formDav.terrestre_tipo, handleTerrestreTipoChange, { deep: true });

watch(
    () => formDav.empreendimento,
    (novoEmpreendimento) => {
        if (!novoEmpreendimento) {
            origemSeiSelecionada.value = "";
            formDav.origem_sei = "";
            return;
        }

        const empSelecionado = props.empreendimentos.find(
            (emp) => emp.cod_emp === novoEmpreendimento
        );

        const sei = empSelecionado?.ose_sei || "";

        origemSeiSelecionada.value = sei;
        formDav.origem_sei = sei;
    },
    { immediate: true }
);

watch(
    () => formDav.profissionais,
    (profissionais) => {
        const datasInicio = profissionais
            .map(p => p.dataInicio)
            .filter(d => d)
            .map(d => new Date(d).getTime());

        const datasFinal = profissionais
            .map(p => p.dataFinal)
            .filter(d => d)
            .map(d => new Date(d).getTime());

        formDav.dataInicio = datasInicio.length
            ? new Date(Math.min(...datasInicio)).toISOString().split("T")[0]
            : "";

        formDav.dataFinal = datasFinal.length
            ? new Date(Math.max(...datasFinal)).toISOString().split("T")[0]
            : "";
    },
    { deep: true }
);


const submitForm = () => {

    const numeroContrato = props.contrato.numero_contrato.replace(
        /^0+\s*|(?<=\s)0+/g,
        ""
    );
    const sequencial = getSequencialDav();
    const empreendimentoFmt = formatarEmpreendimento(formDav.empreendimento);

    console.log(sequencial + empreendimentoFmt)
    formDav.dataInicio = new Date(formDav.dataInicio);
    formDav.dataFinal = new Date(formDav.dataFinal);

    formDav.seq_dav = `DAV - ${sequencial} - ${empreendimentoFmt}`

    const diferencaEmMs = formDav.dataFinal - formDav.dataInicio;

    const qtdDiarias = Math.floor(diferencaEmMs / (1000 * 60 * 60 * 24)) + 1;

    const dadosConsumo = calcularConsumo(
        qtdDiarias,
        formDav.aereo_valor,
        formDav.terrestre_tipo,
        formDav.terrestre_valor,
        formDav.aquatico_valor
    );

    formDav.post(route("sgc.gestao.storeDav"), {
        onSuccess: () => {
            toast.success("Dav cadastrada com sucesso.");
            axios
                .post(route("sgc.gestao.update"), {
                    contrato: numeroContrato,
                    diarias: dadosConsumo.diarias,
                    hatch: dadosConsumo.hatch,
                    pickup: dadosConsumo.pickup,
                    barco: dadosConsumo.barco,
                    aerea: dadosConsumo.aerea,
                })
                .then((response) => {
                    formDav.reset();
                    modalVisualizarForm.value.getBsModal().hide();
                })
                .catch((error) => {
                    console.error(
                        "Erro ao atualizar os dados:",
                        error.response?.data || error.message
                    );
                });
        },
        onError: (errors) => {
            console.error("Erro ao enviar o formulário para storeDav:", errors);
        },
    });
};
defineExpose({ abrirModal });
</script>

<template>
    <Modal ref="modalVisualizarForm" modal-dialog-class="modal-xl modal-dav">
        <template #body>
            <form class="row g-3" @submit.prevent="submitForm">
                <div class="row g-3">
                    <!-- Linha com 3 campos -->
                    <div class="col-md-4">
                        <label for="inputCoordenador" class="form-label"
                            >Coordenador</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="inputCoordenador"
                            v-model="formDav.coordenador"
                        />
                    </div>

                    <div class="col-md-4">
                        <label for="inputEmpreendimento" class="form-label"
                            >Empreendimento</label
                        >
                        <select
                            id="inputEmpreendimento"
                            class="form-select"
                            v-model="formDav.empreendimento"
                        >
                            <option value="">...</option>
                            <option
                                v-for="(
                                    empreendimento, index
                                ) in props.empreendimentos"
                                :key="index"
                                :value="empreendimento.cod_emp"
                            >
                                {{ empreendimento.cod_emp }} -
                                {{ empreendimento.nome }}
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="inputOrigemSei" class="form-label"
                            >SEI</label
                        >
                        <input
                            type="text"
                            class="form-control bg-light"
                            id="inputOrigemSei"
                            v-model="formDav.origem_sei"
                            readonly
                            placeholder="Automático ao selecionar empreendimento"
                        />
                    </div>
                </div>

                <div class="col-12">
                    <label for="inputProduto" class="form-label">Produto</label>
                    <select
                        id="inputProduto"
                        class="form-select"
                        v-model="produtoSelecionado"
                    >
                        <option selected>...</option>
                        <option
                            v-for="(produto, index) of filterProdutos()"
                            :key="index"
                        >
                            {{ produto }}
                        </option>
                    </select>
                </div>
                <div class="col-12">
                    <label for="inputSubproduto" class="form-label"
                        >Subproduto</label
                    >
                    <select
                        id="inputSubproduto"
                        class="form-select"
                        v-model="formDav.subproduto"
                    >
                        <option selected>...</option>
                        <option
                            v-for="(item, index) of subprodutosFiltrados"
                            :key="index"
                        >
                            {{ item.label }}
                        </option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="inputFinalidade" class="form-label"
                        >Finalidade</label
                    >
                    <input
                        type="text"
                        class="form-control"
                        id="inputFinalidade"
                        v-model="formDav.finalidade"
                    />
                </div>
                <div class="col-md-6">
                    <label for="inputEscopo" class="form-label"
                        >Escopo da Atividade</label
                    >
                    <input
                        type="text"
                        class="form-control"
                        id="inputEscopo"
                        v-model="formDav.escopo"
                    />
                </div>

                <!-- Profissionais, Origem e Destino -->
                <div class="col-12">
                    <div
                        v-for="(item, index) in formDav.profissionais"
                        :key="index"
                        class="row g-3 align-items-center mb-2"
                    >
                        <!-- Profissional -->
                        <div class="col-md-6">
                            <label class="form-label">Profissionais</label>
                            <select
                                class="form-select"
                                v-model="
                                    formDav.profissionais[index].profissional
                                "
                            >
                                <option :value="null">Selecione...</option>
                                <option
                                    v-for="p in opcoesProfissionais"
                                    :key="p.id"
                                    :value="{
                                        id: p.id,
                                        nome: p.nome,
                                        formacao: p.formacao,
                                    }"
                                >
                                    {{ `${p.nome} - ${p.formacao}` }}
                                </option>
                            </select>
                        </div>

                        <!-- Data início -->
                        <div class="col-md-3">
                            <label class="form-label">Data início</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="
                                    formDav.profissionais[index].dataInicio
                                "
                            />
                        </div>

                        <!-- Data fim -->
                        <div class="col-md-3">
                            <label class="form-label">Data fim</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="formDav.profissionais[index].dataFinal"
                            />
                        </div>

                        <div class="row g-2 align-items-end">
                            <!-- Origem -->
                            <div class="col-md-5">
                                <label class="form-label">Origem</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Ex: Brasília-DF"
                                    v-model="
                                        formDav.profissionais[index].origem
                                    "
                                />
                            </div>

                            <!-- Destino -->
                            <div class="col-md-4">
                                <label class="form-label">Destino</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Ex: Natal-RN"
                                    v-model="
                                        formDav.profissionais[index].destino
                                    "
                                />
                            </div>

                            <!-- Botões -->
                            <div
                                class="col-md-3 d-flex justify-content-end align-items-end"
                            >
                                <button
                                    type="button"
                                    class="btn btn-success btn-sm"
                                    @click="adicionarProfissional"
                                    v-if="
                                        index ===
                                        formDav.profissionais.length - 1
                                    "
                                >
                                    + Adicionar profissional
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-outline-danger btn-sm ms-3"
                                    @click="removerProfissional(index)"
                                    v-if="formDav.profissionais.length > 1"
                                >
                                    Remover
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Aéreo -->
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckAereo"
                            value="Aéreo"
                            v-model="formDav.transporte"
                            @change="handleTransporteChange"
                        />
                        <label class="form-check-label" for="gridCheckAereo"
                            >Aéreo</label
                        >
                        <input
                            v-if="formDav.transporte.includes('Aéreo')"
                            type="number"
                            v-model="formDav.aereo_valor"
                            max="99"
                            min="0"
                            class="ml-2"
                            style="width: 60px"
                        />
                    </div>

                    <!-- Terrestre -->
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckTerrestre"
                            value="Terrestre"
                            v-model="formDav.transporte"
                            @change="handleTransporteChange"
                        />
                        <label class="form-check-label" for="gridCheckTerrestre"
                            >Terrestre</label
                        >
                        <div
                            v-if="formDav.transporte.includes('Terrestre')"
                            class="ml-3"
                        >
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="gridCheckPickup"
                                    value="Pick-up"
                                    v-model="formDav.terrestre_tipo"
                                    @change="handleTerrestreTipoChange"
                                />
                                <label
                                    class="form-check-label"
                                    for="gridCheckPickup"
                                    >Veículo pick-up</label
                                >
                                <input
                                    v-if="
                                        formDav.terrestre_tipo.includes(
                                            'Pick-up'
                                        )
                                    "
                                    type="number"
                                    v-model="formDav.terrestre_valor"
                                    max="99"
                                    min="0"
                                    class="ml-2"
                                    style="width: 60px"
                                />
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="gridCheckHatch"
                                    value="Hatch"
                                    v-model="formDav.terrestre_tipo"
                                    @change="handleTerrestreTipoChange"
                                />
                                <label
                                    class="form-check-label"
                                    for="gridCheckHatch"
                                    >Veículo hatch</label
                                >
                                <input
                                    v-if="
                                        formDav.terrestre_tipo.includes('Hatch')
                                    "
                                    type="number"
                                    v-model="formDav.terrestre_valor"
                                    max="99"
                                    min="0"
                                    class="ml-2"
                                    style="width: 60px"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Aquático -->
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckAquatico"
                            value="Aquático"
                            v-model="formDav.transporte"
                            @change="handleTransporteChange"
                        />
                        <label class="form-check-label" for="gridCheckAquatico"
                            >Aquático</label
                        >
                        <input
                            v-if="formDav.transporte.includes('Aquático')"
                            type="number"
                            v-model="formDav.aquatico_valor"
                            max="99"
                            min="0"
                            class="ml-2"
                            style="width: 60px"
                        />
                    </div>

                    <!-- Outros -->
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="gridCheckOutros"
                            value="Outros"
                            v-model="formDav.transporte"
                            @change="handleTransporteChange"
                        />
                        <label class="form-check-label" for="gridCheckOutros"
                            >Outros</label
                        >
                    </div>
                </div>
                <div
                    v-if="mostrarCadastroProfissional"
                    class="row g-3 mt-3 border rounded p-3 bg-light"
                >
                    <div class="col-md-5">
                        <label class="form-label">Nome</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="novoProfissional.nome"
                            placeholder="Nome do profissional"
                        />
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Formação</label>
                        <input
                            type="text"
                            class="form-control"
                            v-model="novoProfissional.formacao"
                            placeholder="Formação"
                        />
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            type="button"
                            class="btn btn-success w-100"
                            @click="salvarNovoProfissional"
                        >
                            Adicionar
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <button
                        type="button"
                        class="btn btn-info me-3"
                        @click="
                            mostrarCadastroProfissional =
                                !mostrarCadastroProfissional
                        "
                    >
                        Cadastrar profissionais
                    </button>

                    <button type="submit" class="btn btn-success">
                        Salvar
                    </button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<style scoped>
input[type="number"] {
    margin-left: 0.5rem;
    padding: 0.25rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}
</style>
