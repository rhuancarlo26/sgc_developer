<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconCirclePlus, IconTrash, IconDownload } from "@tabler/icons-vue";

const props = defineProps({
    form: { type: Object },
    tipos: { type: Array },
    contrato: [String, Number],
    produto: String,
});

const adicionarCampo = () => {
    props.form.campos.push({
        nome_campo: null,
        tipo: null,
        obrigatorio: null,
        regra: null,
        valor_min: null,
        valor_max: null,
        max_caracteres: null,
        valor_exemplo: null,
    })
}

const verificarCamposTipo = (key) => {
    const campo = props.form.campos[key]
    if(!campo) return

    const regrasPorTipo = {
        inteiro: ['max_caracteres'],
        decimal: ['max_caracteres'],
        texto: ['valor_min', 'valor_max'],
    }
    
    const todosCampos = ['max_caracteres', 'valor_min', 'valor_max']
    const camposParaResetar = regrasPorTipo[campo.tipo] || todosCampos

    todosCampos.forEach(c => {
        if (camposParaResetar.includes(c)) {
            campo[c] = null
        }
    })
}

const removeCampo = (key) => {
    if(!props.form.campos[key]) return

    props.form.campos.splice(key, 1)
}

const validaCampos = () => {

    let validacoesNulos = false

    props.form.campos.forEach(campos => {

        const keys = Object.keys(campos)
        keys.forEach(key => {

            delete campos[`validaCampo_${key}`]

            if(key === 'obrigatorio') return
            if(key === 'regra') return

            if(['valor_min', 'valor_max'].includes(key)) {

                if(!campos.regra) return

                if(!['inteiro', 'decimal'].includes(campos.tipo)) return
            }

            if(['max_caracteres'].includes(key)) {

                if(!campos.regra) return

                if(!['texto'].includes(campos.tipo)) return
            }

            if(campos[key] === null || campos[key] === '') {
                campos[`validaCampo_${key}`] = true
                validacoesNulos = true
            }
        })
    })

    return validacoesNulos
}

const defineTipoCampo = (tipo) => {
    const tipo_ = props.tipos.find(item => item.value === tipo)
    return tipo_?.tipoInput ?? 'text'
}

const selecionarCheckbox = (campo, tipoCheckbox, { target }) => {
    campo[tipoCheckbox] = target.checked
}

defineExpose({ validaCampos })
</script>

<template>
    <div class="card-header justify-content-between">
        <h3 class="my-0">Campos e Validações</h3>
        <div class="d-flex gap-2">
            <a
                v-if="form.id && form.campos.length"
                :href="route('sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo', [
                    props.contrato,
                    props.produto,
                    form.id,
                ])"
                class="btn bg-gray-700" target="_blank"> 
                <IconDownload class="me-2" /> Gerar Planilha Modelo
            </a>
            <button type="button" @click="adicionarCampo" class="btn btn-secondary"> <IconCirclePlus class="me-2" /> Adicionar Campo</button>
        </div>
    </div>
    <div class="card-body">

        <InputError class="mb-2" :message="form.errors.campos"/>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Nome do Campo</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Obrigatório</th>
                        <th class="text-center">Limite</th>
                        <th class="text-center col-1">Valor Mín</th>
                        <th class="text-center col-1">Valor Máx</th>
                        <th class="text-center col-1">Max Caracteres</th>
                        <th class="text-center">Valor Exemplo</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(c, key) in form.campos" :key="key">
                        <td class="text-center">
                            <input type="text" v-model="c.nome_campo" placeholder="Nome do campo" class="form-control"
                                :class="c.validaCampo_nome_campo ? 'border-danger' : ''"/>
                        </td>
                        <td class="text-center">
                            <select @change="verificarCamposTipo(key)" v-model="c.tipo" class="form-select"
                                :class="c.validaCampo_tipo ? 'border-danger' : ''">
                                <option v-for="t in tipos" :key="t.value" :value="t.value">{{t.label}}</option>
                            </select>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" @change="selecionarCheckbox(c, 'obrigatorio', $event)" :checked="c.obrigatorio ? true : false" class="form-checkbox"/>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" @change="selecionarCheckbox(c, 'regra', $event)" :checked="c.regra" class="form-checkbox"/>
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.valor_min" :disabled="!c.regra || !['inteiro', 'decimal'].includes(c.tipo)" class="form-control" 
                                :class="c.validaCampo_valor_min ? 'border-danger' : ''" :step="c.tipo === 'decimal' ? 'any' : '0'"/>
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.valor_max" :disabled="!c.regra || !['inteiro', 'decimal'].includes(c.tipo)" class="form-control" 
                                :class="c.validaCampo_valor_max ? 'border-danger' : ''" :step="c.tipo === 'decimal' ? 'any' : '0'"/>
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.max_caracteres" :disabled="!c.regra || !['texto'].includes(c.tipo)" class="form-control" 
                                :class="c.validaCampo_max_caracteres ? 'border-danger' : ''"/>
                        </td>
                        <td class="text-center">
                            <input :type="defineTipoCampo(c.tipo)" v-model="c.valor_exemplo" class="form-control" 
                                :class="c.validaCampo_valor_exemplo ? 'border-danger' : ''"/>
                        </td>
                        <td class="text-center">
                            <button type="button" @click="removeCampo(key)" class="btn btn-sm btn-danger">
                                <IconTrash />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
input[type="checkbox"] {
  width: 20px;
  height: 20px;
}
</style>
