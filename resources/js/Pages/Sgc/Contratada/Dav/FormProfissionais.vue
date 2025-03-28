<script setup>
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  contrato: Object,
  profissionais: Object
});

const modalVisualizarForm = ref(null);
const profissionais = ref([]);

const novoProfissional = ref({
  nome: '',
  formacao: '',
  contrato_id: props.contrato.id
});

props.profissionais.map(profissional => (
    profissionais.value.push(profissional)
))

const adicionarProfissional = () => {
  if (novoProfissional.value.nome && novoProfissional.value.formacao) {
    const novo = { ...novoProfissional.value };
    
    profissionais.value.push(novo);

    novoProfissional.value = {
      nome: '',
      formacao: '',
      contrato_id: props.contrato.id
    };
  }
};

const removerProfissional = (index) => {
  profissionais.value.splice(index, 1);
};

const submitForm = async () => {
  if (profissionais.value.length === 0) {
    console.warn("Nenhum profissional para enviar!");
    return;
  }

  const profissionaisData = profissionais.value
  .filter(profissional => !profissional.id) // Filtra os profissionais sem id
  .map(profissional => ({
    nome: profissional.nome,
    formacao: profissional.formacao,
    contrato_id: profissional.contrato_id
  }));

  const form = useForm({
    profissionais: profissionaisData
  });

  try {
    console.log("Enviando dados:", form.data()); // Mostrar os dados do formulário

    await form.post(route('sgc.gestao.storeDavProfissionais'), {
      preserveScroll: true, // Mantém o modal aberto
      onSuccess: () => {
        console.log("Todos os profissionais foram enviados com sucesso!");
      },
      onError: (errors) => {
        console.error("Erro ao enviar profissionais:", errors);
      }
    });

  } catch (error) {
    console.error("Erro ao enviar profissionais:", error);
  }
};

defineExpose({ abrirModal: () => modalVisualizarForm.value.getBsModal().show() });
</script>

<template>
  <Modal ref="modalVisualizarForm" modal-dialog-class="modal-xl">
    <template #body>
      <form class="d-flex align-items-end gap-3" @submit.prevent="adicionarProfissional">
        <div class="w-100">
          <label for="inputNome" class="form-label">Nome</label>
          <input type="text" class="form-control" id="inputNome" v-model="novoProfissional.nome">
        </div>
        <div class="w-100">
          <label for="inputFormacao" class="form-label">Formação</label>
          <input type="text" class="form-control" id="inputFormacao" v-model="novoProfissional.formacao">
        </div>
        <button type="submit" class="btn btn-success">+</button>
      </form>

      <h3 class="mt-4">Profissionais Cadastrados</h3>
      <ul class="list-group">
        <li v-for="(profissional, index) in profissionais" :key="index" class="list-group-item d-flex justify-content-between">
          <span>{{ profissional.nome }} - {{ profissional.formacao }}</span>
          <button class="btn btn-danger btn-sm" @click="removerProfissional(index)">Excluir</button>
        </li>
      </ul>

      <button class="btn btn-primary mt-3" @click="submitForm">Salvar</button>
    </template>
  </Modal>
</template>
