<script setup>
import Breadcrumb from "@/Components/Breadcrumb.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import Navbar from "../Navbar.vue";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import { onMounted, ref, watch } from "vue";

const props = defineProps({
    contrato: Object,
    dav: Object,
    subprodutos: Object,
    produtos: Object,
    empreendimentos: Object,
    consumos: Object,
});

const numeroContrato = props.contrato.numero_contrato.replace(
    /^0+\s*|(?<=\s)0+/g,
    ""
);
const contratoFiltrado = props.consumos.find(
    (item) => item.contrato === numeroContrato
);
const produtoSelecionado = ref("");
const modalVisualizarForm = ref(null);
const profissionais = ref([1]);
const origem = ref([1]);
const destino = ref([1]);
const subprodutosFiltrados = ref([]);

const formDav = useForm({
    seq_dav: props.dav.seq_dav || "",
    empreendimento: props.dav.empreendimento || "",
    coordenador: props.dav.coordenador || "",
    finalidade: props.dav.finalidade || "",
    escopo: props.dav.escopo || "",
    dataInicio: props.dav.dataInicio || "",
    dataFinal: props.dav.dataFinal || "",
    produto: props.dav.produto || "",
    subproduto: props.dav.subproduto || "",
    profissionais: props.dav.profissionais || [
        { profissional: null, origem: "", destino: "" },
    ],
    transporte: props.dav.transporte || [],
    aereo_valor: props.dav.aereo_valor || 0,
    terrestre_tipo: props.dav.terrestre_tipo || [""],
    terrestre_valor: props.dav.terrestre_valor || 0,
    aquatico_valor: props.dav.aquatico_valor || 0,
    status: props.dav.status || "pendente",
    origem_sei: props.dav.origem_sei,
});

onMounted(() => {
    if (props.dav.produto) {
        produtoSelecionado.value = props.dav.produto;
        subprodutosFiltrados.value = props.subprodutos
            .filter((subProduto) =>
                subProduto.subproduto.startsWith(
                    props.dav.produto.split(".")[0] + "."
                )
            )
            .map((subProduto) => ({
                value: subProduto.subproduto,
                label: `${subProduto.subproduto} ${subProduto.descricao_revisada}`,
            }));
    }
});

const adicionarProfissional = () => {
    profissionais.value.push(profissionais.value.length + 1);
};

const filterProdutos = () => {
    const produtos = []; // Array para armazenar os subprodutos filtrados

    const listaExclusao = ["2", "9", "10", "11", "12", "14"];

    for (const produto of props.produtos) {
        if (!listaExclusao.includes(produto.produto)) {
            const descricaoCompleta = `${produto.produto}.${produto.descricao_produto}`;
            produtos.push(descricaoCompleta);
        }
    }

    return produtos;
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

const submitForm = () => {
    formDav.dataInicio = new Date(formDav.dataInicio);
    formDav.dataFinal = new Date(formDav.dataFinal);

    formDav.post(route("sgc.gestao.storeDav"), {
        onSuccess: () => {
            formDav.reset();
            modalVisualizarForm.value.getBsModal().hide();
        },
        onError: (errors) => {
            console.error("Erro ao enviar o formulário:", errors);
        },
    });
};

const aprovarDav = () => {
    formDav.status = "aprovado";
    formDav.put(route("sgc.gestao.aprovarDav", { id: props.dav.id }), {
        onSuccess: () => console.log("DAV aprovada com sucesso!"),
    });
};

const reprovarDav = () => {
    formDav.status = "reprovado";
    formDav.put(route("sgc.gestao.reprovarDav", { id: props.dav.id }), {
        onSuccess: () => console.log("DAV reprovada com sucesso!"),
    });
};
const formatDate = (date) => {
    const [year, month, day] = date.split("-");
    return `${day}/${month}/${year}`;
};

const formatNome = (nome) => {
    return nome
        .toLowerCase()
        .split(" ")
        .map((parte) => parte.charAt(0).toUpperCase() + parte.slice(1))
        .join(" ");
};

const fmtInt = (valor) => {
    if (valor === null || valor === undefined || valor === "") return "0";
    return Math.round(Number(valor)).toString();
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

const gerarPDF = async () => {
    const doc = new jsPDF();

    const diferencaEmMs =
        new Date(formDav.dataFinal) - new Date(formDav.dataInicio);
    const qtdDiarias = Math.floor(diferencaEmMs / (1000 * 60 * 60 * 24)) + 1;
    const margemEsquerda = 15;
    const margemDireita = 15;
    const larguraUtil =
        doc.internal.pageSize.getWidth() - margemEsquerda - margemDireita;
    const tamanhoFonte = 11;
    const espacamentoLinhas = 8;
    doc.setFontSize(tamanhoFonte);

    const adicionarTexto = (texto, x, y, larguraMaxima) => {
        const linhas = doc.splitTextToSize(String(texto || ""), larguraMaxima);
        doc.text(linhas, x, y);
        return linhas.length * espacamentoLinhas;
    };

    const loadImageAsDataUrl = async (url) => {
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error("Não carregou imagem");
            const blob = await res.blob();
            return await new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            });
        } catch (e) {
            return null;
        }
    };

    const imageUrl = "/img/logo/logodnit.png";
    const imgDataUrl = await loadImageAsDataUrl(imageUrl);

    const logoLargura = 40;
    const logoAltura = 30;
    const logoX = doc.internal.pageSize.getWidth() - 30 - logoLargura;
    const logoY = 10;

    if (imgDataUrl) {
        try {
            doc.addImage(imgDataUrl, "PNG", logoX, 13, logoLargura, logoAltura);
        } catch (e) {
            /* ignora */
        }
    }

    doc.setFontSize(17);
    const tituloY = 20 + logoAltura / 2 - 4;

    doc.text("Documento de Autorização de Viagem", margemEsquerda, tituloY);

    doc.setFontSize(12);
    doc.text(`${formDav.seq_dav}`, margemEsquerda, tituloY + 10);

    doc.setFontSize(tamanhoFonte);
    let y = logoY + logoAltura + 10;

    y += adicionarTexto(
        `Contrato: ${props.contrato.numero_contrato}`,
        margemEsquerda,
        y,
        larguraUtil / 2
    );
    adicionarTexto(
        `OSE SEI: ${formDav.origem_sei}`,
        margemEsquerda + larguraUtil / 2,
        y - espacamentoLinhas,
        larguraUtil / 2
    );
    y += adicionarTexto(
        `Coordenador: ${formDav.coordenador}`,
        margemEsquerda,
        y,
        larguraUtil / 2
    );
    adicionarTexto(
        `Empreendimento: ${formDav.empreendimento}`,
        margemEsquerda + larguraUtil / 2,
        y - espacamentoLinhas,
        larguraUtil / 2
    );
    y += adicionarTexto(
        `Produto: ${formDav.produto}`,
        margemEsquerda,
        y,
        larguraUtil
    );
    y += adicionarTexto(
        `Subproduto: ${formDav.subproduto}`,
        margemEsquerda,
        y,
        larguraUtil
    );
    y += adicionarTexto(
        `Finalidade: ${formDav.finalidade}`,
        margemEsquerda,
        y,
        larguraUtil / 2
    );
    adicionarTexto(
        `Escopo: ${formDav.escopo}`,
        margemEsquerda + larguraUtil / 2,
        y - espacamentoLinhas,
        larguraUtil / 2
    );
    y += adicionarTexto(
        `Modal de Transporte: ${
            Array.isArray(formDav.transporte)
                ? formDav.transporte.join(", ")
                : formDav.transporte
        }`,
        margemEsquerda,
        y,
        larguraUtil
    );

    y += 15;

    // ============================
    // TABELA: PROFISSIONAIS (autoTable)
    // ============================
    const headProf = [
        [
            "#",
            "Nome",
            "Formação",
            "Data Início",
            "Data Final",
            "Origem",
            "Destino",
        ],
    ];

    const bodyProf = (
        Array.isArray(formDav.profissionais) ? formDav.profissionais : []
    ).map((p, i) => [
        String(i + 1),
        p.profissional?.nome || "",
        p.profissional?.formacao || "",
        formatDate(p.dataInicio),
        formatDate(p.dataFinal),
        p.origem || "",
        p.destino || "",
    ]);

    autoTable(doc, {
        startY: y,
        head: headProf,
        body: bodyProf,
        theme: "grid",
        tableWidth: larguraUtil, // 👈 força largura total
        headStyles: { fillColor: "#0E3C59", textColor: 255, fontSize: 9 },
        styles: { fontSize: 10, cellPadding: 3, overflow: "linebreak" },
        columnStyles: {
            0: { cellWidth: 10, halign: "center" },
            1: { cellWidth: 35 },
            2: { cellWidth: 30 },
            3: { cellWidth: 25 },
            4: { cellWidth: 25 },
            5: { cellWidth: 30 },
            6: { cellWidth: 30 },
        },
        margin: { left: margemEsquerda, right: margemDireita },
        showHead: "everyPage",
    });

    y = doc.lastAutoTable.finalY;

    const spacingBetweenTables = 12; // ajuste quanto quiser
    y += spacingBetweenTables;

    // ============================
    // TABELA: QUADRO RESUMO (autoTable)
    // ============================
    const headersResumo = [
        [
            "",
            "14.1.1 Diárias",
            "14.1.4 Passagem Aérea",
            "14.1.7 Veículo Aquático",
            "14.1.5 Veículo Hatch",
            "14.1.6 Veículo Pickup",
        ],
    ];

    const terrestreTipos = Array.isArray(formDav.terrestre_tipo)
        ? formDav.terrestre_tipo
        : [formDav.terrestre_tipo];
    const hasHatch = terrestreTipos.includes("Hatch");
    const hasPickup = terrestreTipos.includes("Pick-up");

    const solicitadoRow = [
        "Solicitado",
        fmtInt(qtdDiarias),
        fmtInt(formDav.aereo_valor),
        fmtInt(formDav.aquatico_valor),
        hasHatch ? fmtInt(formDav.terrestre_valor) : "0",
        hasPickup ? fmtInt(formDav.terrestre_valor) : "0",
    ];

    const saldoRow = [
        "Saldo Restante",
        (contratoFiltrado?.diarias || 0).toString(),
        (contratoFiltrado?.aerea || 0).toString(),
        (contratoFiltrado?.barco || 0).toString(),
        (contratoFiltrado?.hatch || 0).toString(),
        (contratoFiltrado?.pickup || 0).toString(),
    ];

    autoTable(doc, {
        startY: y,
        head: headersResumo,
        body: [solicitadoRow, saldoRow],
        theme: "grid",
        tableWidth: larguraUtil, // 👈 mesma largura da tabela acima
        headStyles: { fillColor: "#0E3C59", textColor: 255, fontSize: 9 },
        styles: { fontSize: 11, cellPadding: 4, overflow: "linebreak" },
        columnStyles: {
            0: { cellWidth: 25 }, // descrição
            1: { cellWidth: 30 },
            2: { cellWidth: 35 },
            3: { cellWidth: 35 },
            4: { cellWidth: 30 },
            5: { cellWidth: 30 },
        },
        margin: { left: margemEsquerda, right: margemDireita },
        showHead: "everyPage",
    });

    y = doc.lastAutoTable.finalY;

    // assinaturas
    y += 30;
    doc.setLineWidth(0.5);
    doc.line(margemEsquerda, y, margemEsquerda + larguraUtil / 2 - 10, y);
    doc.text(`Coordenador: ${formDav.coordenador}`, margemEsquerda, y + 10);
    doc.line(
        margemEsquerda + larguraUtil / 2 + 10,
        y,
        margemEsquerda + larguraUtil,
        y
    );
    doc.text(
        `Fiscal: ${formatNome(props.contrato.fiscal_contrato)}`,
        margemEsquerda + larguraUtil / 2 + 10,
        y + 10
    );

    doc.save("detalhes_dav.pdf");
};
</script>

<template>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="w-100 d-flex justify-content-between">
                <Breadcrumb
                    class="align-self-center"
                    :links="[
                        {
                            route: route(
                                'sgc.gestao.listagem',
                                contrato.tipo_contrato
                            ),
                            label: `Gestão de Contratos`,
                        },
                        { route: '#', label: contrato.contratada },
                        {
                            route: route('sgc.gestao.listagemDav', {
                                id: contrato.id,
                            }),
                            label: 'Dav',
                        },
                        { route: '#', label: 'Detalhes' },
                    ]"
                />
            </div>
        </template>

        <Navbar :tipo="contrato">
            <template #body>
                <div class="card">
                    <div class="m-5">
                        <form class="row g-3">
                            <!-- Coordenador e Empreendimento -->
                            <div class="col-md-6">
                                <label for="inputCoordenador" class="form-label"
                                    >Coordenador</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="inputCoordenador"
                                    v-model="formDav.coordenador"
                                    disabled
                                />
                            </div>
                            <div class="col-md-6">
                                <label
                                    for="inputEmpreendimento"
                                    class="form-label"
                                    >Empreendimento</label
                                >
                                <select
                                    id="inputEmpreendimento"
                                    class="form-select"
                                    v-model="formDav.empreendimento"
                                    disabled
                                >
                                    <option selected>...</option>
                                    <option
                                        v-for="(
                                            empreendimento, index
                                        ) of props.empreendimentos"
                                        :key="index"
                                    >
                                        {{ empreendimento.cod_emp }}
                                    </option>
                                </select>
                            </div>

                            <!-- Produto e Subproduto -->
                            <div class="col-12">
                                <label for="inputProduto" class="form-label"
                                    >Produto</label
                                >
                                <select
                                    id="inputProduto"
                                    class="form-select"
                                    v-model="produtoSelecionado"
                                    disabled
                                >
                                    <option selected>...</option>
                                    <option
                                        v-for="(
                                            produto, index
                                        ) of filterProdutos()"
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
                                    disabled
                                >
                                    <option selected>...</option>
                                    <option
                                        v-for="(
                                            item, index
                                        ) of subprodutosFiltrados"
                                        :key="index"
                                    >
                                        {{ item.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Finalidade e Escopo -->
                            <div class="col-md-6">
                                <label for="inputFinalidade" class="form-label"
                                    >Finalidade</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="inputFinalidade"
                                    v-model="formDav.finalidade"
                                    disabled
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
                                    disabled
                                />
                            </div>

                            <!-- Profissionais, Origem e Destino -->
                            <div class="col-12">
                                <div
                                    v-for="(
                                        item, index
                                    ) in formDav.profissionais"
                                    :key="index"
                                    class="row g-3 align-items-center mb-2"
                                >
                                    <div class="col-md-6">
                                        <label class="form-label"
                                            >Profissionais</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="
                                                item.profissional?.nome || ''
                                            "
                                            disabled
                                        />
                                    </div>

                                    <div class="col-md-3">
                                        <label
                                            for="dataInicio"
                                            class="form-label"
                                            >Data início</label
                                        >
                                        <input
                                            type="date"
                                            class="form-control"
                                            id="dataInicio"
                                            :value="item.dataInicio"
                                            disabled
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <label
                                            for="dataFinal"
                                            class="form-label"
                                            >Data final</label
                                        >
                                        <input
                                            type="date"
                                            class="form-control"
                                            id="dataFinal"
                                            :value="item.dataFinal"
                                            disabled
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Origem</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="item.origem"
                                            disabled
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label"
                                            >Destino</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="item.destino"
                                            disabled
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Checkboxes organizados em duas colunas -->
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="gridCheckAereo"
                                        value="Aéreo"
                                        v-model="formDav.transporte"
                                        disabled
                                    />
                                    <label
                                        class="form-check-label"
                                        for="gridCheckAereo"
                                        >Aéreo</label
                                    >
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="gridCheckTerrestre"
                                        value="Terrestre"
                                        v-model="formDav.transporte"
                                        disabled
                                    />
                                    <label
                                        class="form-check-label"
                                        for="gridCheckTerrestre"
                                        >Terrestre</label
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="gridCheckAquatico"
                                        value="Aquático"
                                        v-model="formDav.transporte"
                                        disabled
                                    />
                                    <label
                                        class="form-check-label"
                                        for="gridCheckAquatico"
                                        >Aquático</label
                                    >
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="gridCheckOutros"
                                        value="Outros"
                                        v-model="formDav.transporte"
                                        disabled
                                    />
                                    <label
                                        class="form-check-label"
                                        for="gridCheckOutros"
                                        >Outros</label
                                    >
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-3">
                                <button
                                    type="button"
                                    class="btn btn-success me-2"
                                    @click="aprovarDav"
                                    :disabled="formDav.status === 'aprovado'"
                                >
                                    Aprovar
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    @click="reprovarDav"
                                    :disabled="formDav.status === 'pendente'"
                                >
                                    Reprovar
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-primary ms-2"
                                    @click="gerarPDF"
                                    :disabled="formDav.status === 'pendente'"
                                >
                                    Imprimir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </Navbar>
    </AuthenticatedLayout>
</template>
