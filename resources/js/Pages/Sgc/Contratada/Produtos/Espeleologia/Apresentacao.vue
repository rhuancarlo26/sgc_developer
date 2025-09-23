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
        <!-- Seção de Profissionais -->
        <div class="col-md-12 mb-3">
          <h4>Equipe</h4>
          <div class="mb-3">
            <select v-model="selectedProfissional" class="form-select" @change="$emit('vincular-profissional', selectedProfissional)">
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
import { defineProps, defineEmits, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps(['campanha', 'empreendimentos', 'errors', 'profissionais', 'profissionalRecords']);
const emit = defineEmits(['update-form', 'vincular-profissional', 'salvar-novo-profissional']);

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

const preencherCamposEmpreendimento = () => {
    const emp = props.empreendimentos.find(e => e.cod_emp === props.campanha.cod_emp);
    if (emp) {
        props.campanha.subtrecho = emp.subtrecho || '';
        props.campanha.segmento = emp.segmento || '';
        props.campanha.extensao = emp.extensao || '';
        props.campanha.tipo_de_intervencao = emp.tipo_de_intervencao || '';
        props.campanha.descricao = emp.descricao || '';
        props.campanha.bioma = emp.bioma || '';
    } else {
        props.campanha.subtrecho = '';
        props.campanha.segmento = '';
        props.campanha.extensao = '';
        props.campanha.tipo_de_intervencao = '';
        props.campanha.descricao = '';
        props.campanha.bioma = '';
    }
};

const vincularProfissional = (profissional) => {
    emit('vincular-profissional', profissional);
    selectedProfissional.value = null;
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
};

const excluirProfissional = (id) => {
    props.profissionalRecords = props.profissionalRecords.filter(p => p.id !== id);
};
</script>