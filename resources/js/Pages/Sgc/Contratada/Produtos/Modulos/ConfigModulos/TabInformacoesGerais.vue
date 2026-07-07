<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import axios from "axios";
import Swal from "sweetalert2";
import { ref, computed } from "vue";

const props = defineProps({
    form: { type: Object },
    contrato: [String, Number],
    produto: String,
    produtosDisponiveis: { type: Array, default: () => [] },
});

const loadPlanilhaModelo = ref(false)

const slugify = (texto) => {
    return (texto || '')
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
};

// Opção de criar um novo produto está desativada a pedido do cliente, mas o
// código foi mantido (oculto) para facilitar uma futura reativação.
const permitirCriarProduto = false;

const produtoOption = ref(
    permitirCriarProduto
        ? (props.form.produto_slug
            ? (props.produtosDisponiveis.some(p => p.produto_slug === props.form.produto_slug) ? 'existente' : 'novo')
            : 'nenhum')
        : 'existente'
);

const alterarProdutoOption = (opcao) => {
    produtoOption.value = opcao;

    if (opcao === 'nenhum') {
        props.form.produto_slug = null;
        props.form.produto_titulo = null;
    }
};

const selecionarProdutoExistente = (event) => {
    const slug = event.target.value;
    const produto = props.produtosDisponiveis.find(p => p.produto_slug === slug);

    props.form.produto_slug = produto?.produto_slug ?? null;
    props.form.produto_titulo = produto?.produto_titulo ?? null;
};

const atualizarNovoProdutoTitulo = (event) => {
    props.form.produto_titulo = event.target.value;
    props.form.produto_slug = slugify(event.target.value);
};

const slugPreview = computed(() => props.form.produto_slug || '');

const importarPlanilhaModelo = async ({target}) => {
    const arquivo = target.files?.[0]
    if (!arquivo) return

    if(!props.form.campos.length) {
        await processarCamposPlanilha(arquivo)
        return
    }

    const swal_ = await Swal.fire({
        title: "Existem campos pre cadastrados dentro desse módulo",
        text: "Ao importar uma nova planilha os campos já existentes serão perdidos. Deseja continuar?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
    })

    if(!swal_.isConfirmed) {
        document.getElementById('planilha_modelo').value = ''
        return
    }

    processarCamposPlanilha(arquivo)
}

const processarCamposPlanilha = async (arquivo) => {
    props.form.planilha_modelo = arquivo

    const headers = { 'Content-Type': 'multipart/form-data' }
    const formData = new FormData()
    formData.append('arquivo', props.form.planilha_modelo)

    delete props.form.errors.planilha_modelo

    loadPlanilhaModelo.value = true

    await axios.post(route('sgc.contratada.produtos.modulos.configuracoes.processar-campos-planilha', [
        props.contrato,
        props.produto,
    ]), formData, { headers })
        .then(resp => {
            let campos = resp.data 
            if(!Array.isArray(campos)) {
                campos = Object.values(campos)
            }

            props.form.campos = campos.map(campo => {
                return {
                    nome_campo: campo,
                    tipo: 'texto',
                    obrigatorio: null,
                    regra: null,
                    valor_min: null,
                    valor_max: null,
                    max_caracteres: null,
                    valor_exemplo: null,
                }
            })
        })
        .catch(err => {
            props.form.errors.planilha_modelo = err.response.data?.message
            props.form.planilha_modelo = null

            document.getElementById('planilha_modelo').value = ''
        })

    loadPlanilhaModelo.value = false
}

</script>
<template>
    <div class="card-header">
        <h3 class="my-0">Dados do Módulo</h3>
    </div>
    <div class="card-body">
        <div class="row gap-4">
            <div class="col-12">
                <InputLabel for="nome">
                    <span>Nome do Módulo <span class="text-danger">*</span></span>
                </InputLabel>
                <input type="text" id="nome" name="nome" class="form-control" v-model="form.nome"/>
                <InputError :message="form.errors.nome"/>
            </div>
            <div class="col-12">
                <InputLabel value="Planilha Modelo" for="planilha_modelo"/>
                <div class="d-flex gap-2 align-items-center">
                    <input type="file" id="planilha_modelo" @change="importarPlanilhaModelo" name="planilha_modelo" class="form-control"
                        accept=".xlsx,.csv"/>
                    <div v-if="loadPlanilhaModelo" class="spinner-border text-primary" role="status" style="border-width: 3px">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <InputError :message="form.errors.planilha_modelo"/>
                <small class="text-secondary">Envie uma planilha modelo. Se contiver dados de exemplo, os tipos serão detectados automaticamente.</small>
            </div>

            <div class="col-12">
                <InputLabel>
                    <span>Produto <span class="text-danger">*</span></span>
                </InputLabel>
                <small class="text-secondary d-block mb-2">
                    Vincule este módulo a um produto já existente (ele passa a aparecer no seletor de módulos desse produto).
                </small>

                <div v-if="permitirCriarProduto" class="d-flex gap-3 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="produtoOptionNenhum" value="nenhum"
                            :checked="produtoOption === 'nenhum'" @change="alterarProdutoOption('nenhum')"/>
                        <label class="form-check-label" for="produtoOptionNenhum">Nenhum</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="produtoOptionExistente" value="existente"
                            :checked="produtoOption === 'existente'" @change="alterarProdutoOption('existente')"/>
                        <label class="form-check-label" for="produtoOptionExistente">Vincular a produto existente</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="produtoOptionNovo" value="novo"
                            :checked="produtoOption === 'novo'" @change="alterarProdutoOption('novo')"/>
                        <label class="form-check-label" for="produtoOptionNovo">Criar novo produto</label>
                    </div>
                </div>

                <select v-if="!permitirCriarProduto || produtoOption === 'existente'" class="form-select" required
                    :value="form.produto_slug" @change="selecionarProdutoExistente">
                    <option value="" disabled>Selecione um produto</option>
                    <option v-for="p in produtosDisponiveis" :key="p.produto_slug" :value="p.produto_slug">{{ p.produto_titulo }}</option>
                </select>

                <div v-if="permitirCriarProduto && produtoOption === 'novo'">
                    <input type="text" class="form-control" placeholder="Título do novo produto"
                        :value="form.produto_titulo" @input="atualizarNovoProdutoTitulo"/>
                    <small v-if="slugPreview" class="text-secondary d-block mt-1">Identificador: <code>{{ slugPreview }}</code></small>
                </div>

                <InputError :message="form.errors.produto_slug"/>
                <InputError :message="form.errors.produto_titulo"/>
            </div>
        </div>
    </div>
</template>
