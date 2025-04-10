<script setup>
import { ref, watch, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Head } from "@inertiajs/vue3";
import axios from "axios";
import { reactive } from "vue";

const props = defineProps({
  evolucao: Array,
  empreendimento_id: Number,
  fases: Array,
  licenciamento: String
});
const form = useForm({
  fase: "",
  status: "ativo",
  periodo: "",
  empreendimento_id: props.empreendimento_id,
});

const submit = () => {
  const formData = new FormData();
  formData.append("contrato", 1);
  formData.append("empreendimento_id", props.empreendimento_id);
  formData.append("fase", form.fase);
  formData.append("status", form.status);
  formData.append("periodo", form.periodo);

  axios
    .post(
      // sgc.contratada.dashboard.searchempreendimentos
      route("sgc.contratada.dashboard.novafase", {
        contrato: 1,
      }),
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      }
    )
    .then((response) => {
    //   console.log(response.data);
        location.reload();
    });
};
// Criar uma cópia reativa de evolucao
const evolucaoLocal = ref([...props.fases]);

// Sincronizar a cópia local com as props quando elas mudarem
watch(
  () => props.fases,
  (newVal) => {
    evolucaoLocal.value = [...newVal];
  }
);

// Item selecionado
const selectedItemIndex = ref(null);

// Função para abrir o diálogo/modal
const openDialog = (index) => {
  selectedItemIndex.value = index; // Armazenamos o índice do item selecionado
  const modal = new bootstrap.Modal(
    document.getElementById("changeStatusModal")
  );
  modal.show();
};

// Função para confirmar a mudança de status
const confirmChangeStatus = () => {
  if (selectedItemIndex.value !== null) {
    // Alterar o status diretamente no item original da cópia local
    evolucaoLocal.value[selectedItemIndex.value].status = evolucaoLocal.value[selectedItemIndex.value].status ? 0 : 1;

    const formData = new FormData();
    formData.append("contrato", 1);
    formData.append("id", evolucaoLocal.value[selectedItemIndex.value].id);
    formData.append("status", evolucaoLocal.value[selectedItemIndex.value].status);

    axios.post(
        route("sgc.contratada.dashboard.updatefase", {contrato: 1}),
        formData,
        { headers: { "Content-Type": "multipart/form-data" }
    }).then((response) => {
        // location.reload();
    });

    const modal = bootstrap.Modal.getInstance(
      document.getElementById("changeStatusModal")
    );
    modal.hide();

    // Limpar o item selecionado após a mudança
    selectedItemIndex.value = null;
  }
};

const evolucaoComStatusAjustado = computed(() =>
  evolucaoLocal.value.map((item, index, array) => {
    if (index < array.length - 1 && (array[index + 1].status === 1 || array[index + 1].status === 2)) {
      return { ...item, status: 1 };
    }
    return item;
  })
);
</script>
<template>
  <div class="container">
    <div class="row text-center justify-content-center mb-5">
      <div class="col-md-12 my-2">
        <hr>
        <h2 class="font-weight-bold">{{ licenciamento }}</h2>

        <!-- <p class="text-muted">
            <button
                type="button"
                class="btn btn-default text-bolder"
                data-bs-toggle="modal"
                data-bs-target="#projetoModal"
                >
                <strong>Cadastrar nova fase:</strong>
            </button>
        </p> -->
      </div>
    </div>

    <div class="row">
      <div
        class="modal fade"
        id="projetoModal"
        tabindex="-1"
        aria-labelledby="projetoModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="projetoModalLabel">Criar Fase</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <!-- <h1>Criar Fase</h1> -->

              <!-- <div v-if="flash.message" class="alert alert-success">
                {{ flash.message }}
                </div> -->

              <form @submit.prevent="submit">
                <div class="row">
                  <div class="col-md-12 my-3">
                    <label class="form-label" for="fase">Fase:</label>
                    <input
                      class="form-control"
                      type="text"
                      v-model="form.fase"
                      id="fase"
                      required
                    />
                    <!-- <span v-if="errors.fase">{{ errors.fase }}</span> -->
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="status">Status:</label>
                    <select
                      class="form-select"
                      v-model="form.status"
                      id="status"
                      required
                    >
                      <option value="1">Ativo</option>
                      <option value="0">Inativo</option>
                      <!-- <option value="em espera">Em espera</option> -->
                    </select>
                    <!-- <span v-if="errors.status">{{ errors.status }}</span> -->
                  </div>

                  <div class="col-md-6">
                    <label class="form-label" for="periodo">Período:</label>
                    <input
                      type="text"
                      v-model="form.periodo"
                      class="form-control"
                      id="periodo"
                      required
                    />
                    <!-- <span v-if="errors.periodo">{{ errors.periodo }}</span> -->
                  </div>
                  <div class="col-md-12 my-4">
                    <button type="submit" class="btn btn-success">
                      Salvar
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="timeline-steps aos-init aos-animate" data-aos="fade-up">
          <!-- Empreendimento ID: {{ empreendimento_id }} -->
          <div
            v-for="(item, index) in evolucaoComStatusAjustado"
            :key="item.id"
            class="timeline-step my-7"
            :class="'timeline-content'"
          >
            <p class="h3 mb-0" style="position: absolute; bottom: 100px">

              {{ item.fase }}
            </p>
            <div
              class="my-5"
              style="position: absolute; bottom: 110px; font-size: 40px;"
            >
              🗠
            </div>
            <div
              v-if="item.status == 'ativo' || item.status == 1"
              class="my-5"
              style="
                font-weight: bolder;
                position: absolute;
                bottom: 6px;
                font-size: 20px;
                color: white;
              "
            >
              🗸
              <sup class="badge bg-lightgreen fs-6 text-gray" style="position: absolute;margin-top:-15px;z-index:1">Concluído</sup>
            </div>
            <div
              v-if="item.status == 2"
              class="my-5"
              style="
                font-weight: bolder;
                position: absolute;
                bottom: 6px;
                font-size: 20px;
                color: white;
              "
            >
              ©
              <sup class="badge bg-lightblue fs-6 text-gray" style="position: absolute;margin-top:-15px;z-index:1">Em andamento</sup>
            </div>
            <div
              v-if="item.status == 0"
              class="my-5"
              style="
                font-weight: bolder;
                position: absolute;
                bottom: 6px;
                font-size: 20px;
                color: white;
              "
            >
              •
              <sup class="badge bg-lightgray fs-6 text-gray" style="position: absolute;margin-top:-15px;z-index:1">Não iniciado</sup>
            </div>
            <div
              :class="'timeline-content'"
              data-toggle="popover"
              data-trigger="hover"
              data-placement="top"
              title=""
              data-content="Tudo certo"
              data-original-title="2024"
            >
                <div :class="'inner-circle ' + (item.status == 0 ? 'bg-gray' : '') + (item.status == 2 ? ' bg-blue' : '')" >
                </div>
                <!-- @click="openDialog(index)" -->
              <p class="h5 text-muted mb-0 mt-2 mb-lg-0">{{ item.periodo }}</p>
            </div>
            <!-- <div
              :class="[
                'timeline-content',
                { 'inner-circle': true, 'bg-gray': item.status == 0 },
              ]"
              @click="openDialog(index)"
            >
              <div
                :class="'inner-circle ' + (item.status == 0 ? 'bg-gray' : '')"
              ></div>
            </div> -->
          </div>
          <!-- Modal para mudar o status -->
          <div class="modal fade" id="changeStatusModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Alterar Status</h5>
                  <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  ></button>
                </div>
                <div class="modal-body">
                  <p>Você deseja mudar o status deste item?</p>
                </div>
                <div class="modal-footer">
                  <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                  >
                    Cancelar
                  </button>
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="confirmChangeStatus"
                  >
                    Confirmar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
/* body{margin-top:20px;} */
.timeline-steps {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
}

.timeline-steps .timeline-step {
  align-items: center;
  display: flex;
  flex-direction: column;
  position: relative;
  margin: 1rem;
}

@media (min-width: 768px) {
  .timeline-steps .timeline-step:not(:last-child):after {
    content: "";
    display: block;
    border-top: 0.25rem solid gray;
    width: 3.46rem;
    position: absolute;
    left: 7.5rem;
    top: 0.5rem;
    opacity: 0.3;
  }
  .timeline-steps .timeline-step:not(:first-child):before {
    content: "";
    display: block;
    border-top: 0.25rem solid gray;
    width: 3.55rem;
    position: absolute;
    right: 7.5rem;
    top: 0.5rem;
    opacity: 0.3;
  }
}

.timeline-steps .timeline-content {
  width: 10rem;
  text-align: center;
}

.timeline-steps .timeline-content .inner-circle {
  border-radius: 1.5rem;
  height: 1.5rem;
  width: 1.5rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background-color: lime;
}
.bg-lightgreen {
    background-color: lightgreen !important;
    box-shadow: 2px 2px 2px 2px rgba(0,0,0,.2);
}
.bg-lightgray {
    background-color: lightgray !important;
    box-shadow: 2px 2px 2px 2px rgba(0,0,0,.2);
}
.bg-lightblue {
    background-color: lightcyan !important;
    box-shadow: 2px 2px 2px 2px rgba(0,0,0,.2);
}

.timeline-steps .timeline-content .inner-circle:before {
  content: "";
  background-color: gray;
  display: inline-block;
  height: 3rem;
  width: 3rem;
  min-width: 3rem;
  border-radius: 6.25rem;
  opacity: 0.3;
}

.bg-gray {
  background-color: gray !important;
}
.bg-blue {
  background-color: cyan !important;
}
.timeline-step:hover {
  cursor: pointer;
}
</style>
