<script setup>
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { IconCirclePlus, IconTrash } from "@tabler/icons-vue";

const props = defineProps({
    form: { type: Object },
});

const tipos = [
    { label: 'Texto', value: 'texto' },
    { label: 'Inteiro', value: 'inteiro' },
    { label: 'Decimal', value: 'decimal' },
    { label: 'Data', value: 'data' },
]

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
</script>

<template>
    <div class="card-header justify-content-between">
        <h3 class="my-0">Campos e Validações</h3>
        <button type="button" @click="adicionarCampo" class="btn btn-secondary"> <IconCirclePlus class="me-2" /> Adicionar Campo</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Nome do Campo</th>
                        <th class="text-center">Tipo</th>
                        <th class="text-center">Obrigatório</th>
                        <th class="text-center">Regra</th>
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
                            <input type="text" v-model="c.nome_campo" class="form-control" placeholder="Nome do campo"/>
                        </td>
                        <td class="text-center">
                            <select @change="verificarCamposTipo(key)" v-model="c.tipo" class="form-select">
                                <option v-for="t in tipos" :key="t.value" :value="t.value">{{t.label}}</option>
                            </select>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" v-model="c.obrigatorio" class="form-checkbox"/>
                        </td>
                        <td class="text-center align-middle">
                            <input type="checkbox" v-model="c.regra" class="form-checkbox"/>
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.valor_min" class="form-control" :disabled="!c.regra || !['inteiro', 'decimal'].includes(c.tipo)" />
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.valor_max" class="form-control" :disabled="!c.regra || !['inteiro', 'decimal'].includes(c.tipo)" />
                        </td>
                        <td class="text-center">
                            <input type="number" v-model="c.max_caracteres" class="form-control" :disabled="!c.regra || !['texto'].includes(c.tipo)" />
                        </td>
                        <td class="text-center">
                            <input type="text" v-model="c.valor_exemplo" class="form-control"/>
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
