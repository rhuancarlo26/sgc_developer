<script setup>
import { defineProps, defineEmits, ref, nextTick, onMounted } from 'vue';
import MapSgc from '@/Components/MapSgc.vue';

const props = defineProps({
  empreendimento: Object,
  subproduto: String,
  paipaId: [Number, String],
  paipa: { type: Object, default: null },
  profissionais: { type: Array, default: () => [] },
});

const mapaVisualizarTrecho = ref();
const rhSelecionadoId = ref('');
const justificativaForm = ref({
  codigo_sei: props.paipa?.justificativa_sei ?? '',
  obs: props.paipa?.justificativa_complementar ?? '',
});
const profissionalSelecionadoId = ref('');
const mostrarFormularioProfissional = ref(false);

const equipeForm = ref({
  nome: '',
  cpf: '',
  cnpj: '',
  email: '',
  profissao: '',
  funcao: '',
  conselho_classe: '',
  numero_registro: '',
  carteira_profissional: '',
  ct: '',
  tipo_participacao: '',
  obs: '',
})

const equipe = ref(
  props.paipa?.equipe?.map(membro => ({
    id: membro.id,
    nome: membro.nome,
    cpf: membro.cpf,
    cnpj: membro.cnpj,
    email: membro.email,
    profissao: membro.profissao,
    funcao: membro.funcao,
    conselho_classe: membro.conselho_classe,
    numero_registro: membro.numero_registro,
    carteira_profissional: membro.carteira_profissional,
    ct: membro.ct,
    obs: membro.obs,
    tipo_participacao: membro.pivot?.tipo_participacao ?? '',
    ativo: membro.pivot?.ativo ?? true,
  })) ?? []
);

const visualizarTrecho = async () => {
  await nextTick();

  if (!props.empreendimento?.coordenadas || !mapaVisualizarTrecho.value) return;
  mapaVisualizarTrecho.value.renderMapa();

  setTimeout(() => {
    mapaVisualizarTrecho.value.setGeoJson(props.empreendimento.coordenadas);
  }, 500);
}

onMounted(() => {
  visualizarTrecho();
})

function formatarSubtrecho(empreendimento) {
  if (!empreendimento) return 'Dados não encontrados';

  const subtrecho = [];

  if (empreendimento.subtrecho_ini && empreendimento.subtrecho_fin) {
    subtrecho.push(`${empreendimento.subtrecho_ini} - ${empreendimento.subtrecho_fin}`);
  }

  if (empreendimento.subtrecho_ini2 && empreendimento.subtrecho_fin3) {
    subtrecho.push(`${empreendimento.subtrecho_ini2} - ${empreendimento.subtrecho_fin3}`);
  }

  if (empreendimento.subtrecho_ini3 && empreendimento.subtrecho_fin32) {
    subtrecho.push(`${empreendimento.subtrecho_ini3} - ${empreendimento.subtrecho_fin32}`);
  }

  return subtrecho.length > 0 ? subtrecho.join(' / ') : 'Segmento não informado';
}

function formatarSegmento(empreendimento) {
  if (!empreendimento) return 'Dados não encontrados';

  const segmentos = [];

  if (empreendimento.km_ini && empreendimento.km_fin) {
    segmentos.push(`km ${empreendimento.km_ini} ao km ${empreendimento.km_fin}`);
  }

  if (empreendimento.km_ini2 && empreendimento.km_fin2) {
    segmentos.push(`km ${empreendimento.km_ini2} ao km ${empreendimento.km_fin2}`);
  }

  if (empreendimento.km_ini3 && empreendimento.km_fin3) {
    segmentos.push(`km ${empreendimento.km_ini3} ao km ${empreendimento.km_fin3}`);
  }

  return segmentos.length > 0 ? segmentos.join(' / ') : 'Segmento não informado';
}

const adicionarEquipe = () => {
  if (!equipeForm.value.nome) return;

  equipe.value.push({
    ...equipeForm.value,
    ativo: true
  });

  equipeForm.value = {
    nome: '',
    cpf: '',
    cnpj: '',
    email: '',
    profissao: '',
    funcao: '',
    conselho_classe: '',
    numero_registro: '',
    carteira_profissional: '',
    ct: '',
    tipo_participacao: '',
    obs: '',
  };
};

const removerMembroEquipe = (id) => {
  equipe.value = equipe.value.filter(membro => membro.id !== id);
}

const adicionarProfissionalExistente = () => {
  const profissional = props.profissionais.find(
    item => Number(item.id) === Number(profissionalSelecionadoId.value)
  );

  if (!profissional) {
    return;
  }

  const jaAdicionado = equipe.value.some(
    membro => Number(membro.id) === Number(profissional.id)
  );

  if (jaAdicionado) {
    profissionalSelecionadoId.value = '';
    return;
  }

  equipe.value.push({
    ...profissional,
    tipo_participacao: '',
    ativo: true,
  });

  profissionalSelecionadoId.value = '';
};

const salvarEAvancar = () => {
  emit('salvar-apresentacao', {
    paipa_id: props.paipaId,
    empreendimento_id: props.empreendimento.id,
    justificativa_sei: justificativaForm.value.codigo_sei,
    justificativa_complementar: justificativaForm.value.obs,
    equipe: equipe.value,
  });
};

const emit = defineEmits(['voltar', 'salvar-apresentacao']);
</script>

<template>
  <div class="row align-items-stretch">
    <div class="col-md-7 mb-3">
      <MapSgc
        v-if="empreendimento?.coordenadas"
        ref="mapaVisualizarTrecho"
        height="400px"
        width="100%"
        :manual-render="true"
      />

      <div v-else class="text-muted text-center p-4 border rounded">
        Nenhuma coordenada disponível para o empreendimento.
      </div>
    </div>

    <div class="col-md-5 mb-3">
      <div class="card h-100">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><strong>BR/UF:</strong> {{ empreendimento?.br }}/{{ empreendimento?.uf }}</li>
          <li class="list-group-item"><strong>Subtrecho:</strong> {{ formatarSubtrecho(empreendimento) }}</li>
          <li class="list-group-item"><strong>Segmento:</strong> {{ formatarSegmento(empreendimento) }}</li>
          <li class="list-group-item"><strong>Extensão:</strong> {{ empreendimento?.extensao }}</li>
          <li class="list-group-item"><strong>Tipo de Intervenção:</strong> {{ empreendimento?.tipo_de_intervencao }}</li>
          <li class="list-group-item"><strong>Descrição:</strong> {{ empreendimento?.descricao }}</li>
          <li class="list-group-item"><strong>Bioma:</strong> {{ empreendimento?.bioma }}</li>
        </ul>
      </div>
    </div>

    <div class="mt-4">
      <h4>Equipe</h4>

      <select v-model="profissionalSelecionadoId" class="form-select">
        <option value="">Selecione um profissional</option>
        <option
          v-for="profissional in profissionais"
          :key="profissional.id"
          :value="profissional.id"
        >
          {{ profissional.nome }} - {{ profissional.profissao || 'Formação não informada' }}
        </option>
      </select>

      <button
        type="button"
        class="btn btn-success mt-3"
        :disabled="!profissionalSelecionadoId"
        @click="adicionarProfissionalExistente"
      >
        Adicionar
      </button>

      <button
        type="button"
        class="btn btn-secondary ms-3 mt-3"
        @click="mostrarFormularioProfissional = !mostrarFormularioProfissional"
      >
        Cadastrar novo
      </button>

      <div v-if="mostrarFormularioProfissional" class="row g-3">
        <div class="col-md-4">
          <label for="nome" class="form-label">Nome</label>
          <input v-model="equipeForm.nome" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label for="cpf" class="form-label">CPF</label>
          <input v-model="equipeForm.cpf" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label for="cnpj" class="form-label">CNPJ</label>
          <input v-model="equipeForm.cnpj" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label class="form-label">E-mail</label>
          <input v-model="equipeForm.email" type="email" class="form-control" />
        </div>

        <div class="col-md-4">
          <label class="form-label">Profissão/Formação</label>
          <input v-model="equipeForm.profissao" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label class="form-label">Função</label>
          <input v-model="equipeForm.funcao" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label class="form-label">Tipo de participação</label>
          <select v-model="equipeForm.tipo_participacao" class="form-select">
            <option value="">Selecione</option>
            <option value="responsavel_tecnico">Responsável Técnico</option>
            <option value="coordenador">Coordenador</option>
            <option value="assistente">Assistente</option>
            <option value="consultor">Consultor</option>
            <option value="fiscal">Fiscal</option>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Conselho de classe</label>
          <input v-model="equipeForm.conselho_classe" type="text" class="form-control" />
        </div>

        <div class="col-md-4">
          <label class="form-label">Número de registro</label>
          <input v-model="equipeForm.numero_registro" type="text" class="form-control" />
        </div>

        <div class="col-md-12">
          <label class="form-label">Observação</label>
          <textarea v-model="equipeForm.obs" class="form-control"></textarea>
        </div>

        <div class="col-md-12 text-end">
          <button type="button" class="btn btn-success" @click="adicionarEquipe">
            Adicionar membro
          </button>
        </div>

      </div>

      <div class="table-responsive" v-if="equipe.length">
      <table class="table table-hover mt-4">
        <thead>
        <tr>
          <th>Nome</th>
          <th>CPF/CNPJ</th>
          <th>Profissão</th>
          <th>Função</th>
          <th>Participação</th>
          <th class="text-center">Ação</th>
        </tr>
        </thead>

        <tbody>
        <tr v-for="membro in equipe" :key="membro.id">
          <td>{{ membro.nome }}</td>
          <td>{{ membro.cpf || membro.cnpj }}</td>
          <td>{{ membro.profissao }}</td>
          <td>{{ membro.funcao }}</td>
          <td>{{ membro.tipo_participacao }}</td>
          <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" @click="removerMembroEquipe(membro.id)">
              Remover
            </button>
          </td>
        </tr>
        </tbody>
      </table>
      </div>

      <div class="mt-4">
        <h4>1.3. Justificativa</h4>

        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <label class="form-label">
              Código SEI-DNIT do TRE do Órgão Licenciador
            </label>

            <input
              v-model="justificativaForm.codigo_sei"
              type="text"
              class="form-control"
              placeholder="Informe o código SEI-DNIT"
            />

            <small class="text-muted">
              O código é numérico, mas deve ser armazenado como texto.
            </small>
          </div>

          <div class="col-md-6">
            <label class="form-label">
              Observação
            </label>

            <input
              v-model="justificativaForm.obs"
              type="text"
              class="form-control"
              placeholder="Informe a observação"
            />
            <small class="text-muted">
              Observação é opcional
            </small>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-between mt-4">
        <button
          type="button"
          class="btn btn-secondary"
          @click="emit('voltar')"
        >
          Voltar
        </button>

        <button
          type="button"
          class="btn btn-primary"
          @click="salvarEAvancar"
        >
          Avançar
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
