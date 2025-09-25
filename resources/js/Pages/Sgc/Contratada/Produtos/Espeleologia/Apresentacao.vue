<template>
  <div>
    <form>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label>ID Campanha</label>
          <input v-model="campanha.id_campanha" class="form-control" readonly />
          <small v-if="errors.id_campanha" class="text-danger">{{ errors.id_campanha }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Empreendimento</label>
          <select v-model="campanha.cod_emp" @change="preencherCamposEmpreendimento" class="form-select" required>
            <option value="">Selecione um empreendimento</option>
            <option v-for="emp in empreendimentos" :key="emp.cod_emp" :value="emp.cod_emp">{{ emp.cod_emp }}</option>
          </select>
          <small v-if="errors.cod_emp" class="text-danger">{{ errors.cod_emp }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Subproduto</label>
          <input v-model="campanha.subproduto" class="form-control" readonly />
          <small v-if="errors.subproduto" class="text-danger">{{ errors.subproduto }}</small>
        </div>
        <div class="col-md-6 mb-3">
          <label>Subtrecho</label>
          <input v-model="campanha.subtrecho" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Segmento</label>
          <input v-model="campanha.segmento" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Extensão (km)</label>
          <input v-model="campanha.extensao" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Tipo de Intervenção</label>
          <input v-model="campanha.tipo_de_intervencao" class="form-control" readonly />
        </div>
        <div class="col-md-6 mb-3">
          <label>Bioma</label>
          <input v-model="campanha.bioma" class="form-control" readonly />
        </div>
        <div class="col-md-12 mb-3">
          <label>Descrição</label>
          <textarea v-model="campanha.descricao" class="form-control" readonly />
        </div>
        <!-- Seção de Equipe -->
        <div class="col-md-12 mb-3">
          <h4>Equipe</h4>
          <div class="mb-3">
            <select v-model="selectedProfissional" class="form-select" @change="vincularProfissional">
              <option value="">Selecione um profissional</option>
              <option v-for="prof in profissionais" :key="prof.id" :value="prof">
                {{ prof.profissional }}
              </option>
            </select>
            <button @click.prevent="showModal = true" class="btn btn-secondary mt-2">Cadastrar Novo Profissional</button>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Profissional</th>
                <th>Formação</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="prof in profissionalRecords" :key="prof.id">
                <td>{{ prof.profissional }}</td>
                <td>{{ prof.formacao }}</td>
                <td><button @click="excluirProfissional(prof.id)" class="btn btn-danger btn-sm">Excluir</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Seção de Justificativas -->
        <div class="col-md-12 mb-3">
          <h4>Justificativas</h4>
          <div class="mb-3">
            <label>Código SEI-DNIT do TRE do Órgão Licenciador</label>
            <input v-model="codigoSei" @input="$emit('update:codigo-sei', $event.target.value)" class="form-control" />
          </div>
          <div v-for="(just, index) in justificativas" :key="index" class="mb-3">
            <div class="row">
              <div v-if="index === 0" class="col-md-12 mb-2">
                <label>Citação</label>
                <input v-model="justificativas[index].titulo" @input="$emit('update:justificativas', [...justificativas.value.slice(0, index), { ...just, titulo: $event.target.value }, ...justificativas.value.slice(index + 1)])" class="form-control mb-2" placeholder="Título da Citação">
                <textarea v-model="justificativas[index].justificativa" @input="$emit('update:justificativas', [...justificativas.value.slice(0, index), { ...just, justificativa: $event.target.value }, ...justificativas.value.slice(index + 1)])" class="form-control" placeholder="Texto da Citação"></textarea>
              </div>
              <div v-else class="col-md-12 mb-2">
                <label>Justificativa</label>
                <textarea v-model="justificativas[index].justificativa" @input="$emit('update:justificativas', [...justificativas.value.slice(0, index), { ...just, justificativa: $event.target.value }, ...justificativas.value.slice(index + 1)])" class="form-control" placeholder="Texto da Justificativa"></textarea>
              </div>
              <div class="col-md-12 text-end">
                <button @click="excluirJustificativa(index)" class="btn btn-danger btn-sm" v-if="index > 0">Remover</button>
              </div>
            </div>
          </div>
          <button @click="adicionarJustificativa" class="btn btn-secondary mt-2">Adicionar Justificativa</button>
        </div>
      </div>
    </form>

    <!-- Modal para Cadastrar Novo Profissional -->
    <div v-if="showModal" class="modal" tabindex="-1" style="display: block;">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cadastrar Profissional</h5>
            <button type="button" class="btn-close" @click="showModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Profissional</label>
              <input v-model="novoProfissional.profissional" class="form-control" required />
            </div>
            <div class="mb-3">
              <label>Formação</label>
              <input v-model="novoProfissional.formacao" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Telefone</label>
              <input v-model="novoProfissional.telefone" class="form-control" />
            </div>
            <div class="mb-3">
              <label>CPF</label>
              <input v-model="novoProfissional.cpf" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input v-model="novoProfissional.email" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Currículo Lattes</label>
              <input v-model="novoProfissional.curriculum_lattes" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Função</label>
              <input v-model="novoProfissional.funcao" class="form-control" />
            </div>
            <div class="mb-3">
              <label>CTF</label>
              <input v-model="novoProfissional.ctf" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Validade</label>
              <input v-model="novoProfissional.validade" type="date" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Conselho de Classe</label>
              <input v-model="novoProfissional.conselho_de_classe" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Número de Registro</label>
              <input v-model="novoProfissional.numero_de_registro" type="number" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Status</label>
              <input v-model="novoProfissional.status" class="form-control" />
            </div>
            <div class="mb-3">
              <label>Observação</label>
              <textarea v-model="novoProfissional.observacao" class="form-control"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showModal = false">Fechar</button>
            <button @click="salvarNovoProfissional" class="btn btn-primary">Salvar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps(['campanha', 'empreendimentos', 'errors', 'profissionais', 'profissionalRecords', 'justificativas', 'codigoSei']);
const emit = defineEmits(['update-form', 'vincular-profissional', 'salvar-novo-profissional', 'update:codigo-sei', 'update:justificativas', 'excluir-profissional']);

const showModal = ref(false);
const selectedProfissional = ref(null);
const novoProfissional = ref({
    profissional: '',
    formacao: '',
    telefone: '',
    cpf: '',
    email: '',
    curriculum_lattes: '',
    funcao: '',
    ctf: '',
    validade: '',
    conselho_de_classe: '',
    numero_de_registro: '',
    status: 'Ativo',
    observacao: '',
});

const codigoSei = ref(props.codigoSei || '');
const justificativas = ref(props.justificativas || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: props.codigoSei || '' }]);

const preencherCamposEmpreendimento = () => {
    const emp = props.empreendimentos.find(e => e.cod_emp === props.campanha.cod_emp);
    if (emp) {
        emit('update-form', {
            subtrecho: emp.subtrecho || '',
            segmento: emp.segmento || '',
            extensao: emp.extensao || '',
            tipo_de_intervencao: emp.tipo_de_intervencao || '',
            descricao: emp.descricao || '',
            bioma: emp.bioma || '',
        });
    } else {
        emit('update-form', {
            subtrecho: '',
            segmento: '',
            extensao: '',
            tipo_de_intervencao: '',
            descricao: '',
            bioma: '',
        });
    }
};

const vincularProfissional = () => {
    if (selectedProfissional.value) {
        emit('vincular-profissional', selectedProfissional.value);
        selectedProfissional.value = null;
    }
};

const salvarNovoProfissional = () => {
    emit('salvar-novo-profissional', { ...novoProfissional.value });
    novoProfissional.value = {
        profissional: '',
        formacao: '',
        telefone: '',
        cpf: '',
        email: '',
        curriculum_lattes: '',
        funcao: '',
        ctf: '',
        validade: '',
        conselho_de_classe: '',
        numero_de_registro: '',
        status: 'Ativo',
        observacao: '',
    };
    showModal.value = false;
};

const adicionarJustificativa = () => {
    justificativas.value.push({ justificativa: '', tipo: 'justificativa', titulo: '', codigo_sei: '' });
};

const removerJustificativa = (index) => {
    justificativas.value.splice(index, 1);
};

const excluirProfissional = (id) => {
    emit('excluir-profissional', id);
};

watch(() => props.codigoSei, (newVal) => {
    codigoSei.value = newVal || '';
});

watch(() => props.justificativas, (newVal) => {
    justificativas.value = newVal || [{ justificativa: '', tipo: 'citacao', titulo: '', codigo_sei: codigoSei.value }];
}, { deep: true });
</script>