<script setup>
import Table from '@/Components/Table.vue';
import { router, useForm } from "@inertiajs/vue3";
import { IconDoorExit, IconDeviceFloppy, IconDots } from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { dateTimeFormat } from "@/Utils/DateTimeUtils";
import { IconTrash } from "@tabler/icons-vue";
import { IconPencil } from "@tabler/icons-vue";

const props = defineProps({
  contrato: Object,
  veiculo: Object,
  codigos: Array
});

const form_quilometragem = useForm({
  id: null,
  contrato_id: props.contrato?.id,
  recurso_veiculo_id: props.veiculo?.id,
  km_referencia: null,
  mes_referencia: null
});

const salvarQuilometragem = () => {
  if (form_quilometragem.id) {
    form_quilometragem.patch(route('contratos.contratada.recurso.veiculo.update_quilometragem'), {
      onSuccess: () => form_quilometragem.reset()
    });
  } else {
    form_quilometragem.post(route('contratos.contratada.recurso.veiculo.store_quilometragem'), {
      onSuccess: () => form_quilometragem.reset()
    });
  }
}

const editarKm = (km) => {
  const parts = km.mes_referencia.split('-');

  km.mes_referencia = `${parts[0]}-${parts[1]}`;

  Object.assign(form_quilometragem, km)
}

const destroyKm = (km_id) => {
  router.delete(route('contratos.contratada.recurso.veiculo.destroy_quilometragem', km_id));
}

</script>
<template>
  <div class="mb-4">
    <h2>
      <p>Km inicial: {{ parseFloat(veiculo.km_inicial).toFixed(2) }} - Km final {{
        parseFloat(veiculo.group_data_km?.km_total).toFixed(2) }} = Km
        total {{ parseFloat(veiculo.km_inicial - veiculo.group_data_km?.km_total).toFixed(2) }}</p>
    </h2>
  </div>
  <div class="mb-4">
    <form @submit.prevent="salvarQuilometragem()">
      <div class="row">
        <div class="col">
          <InputLabel value="Quilometragem" for="km_referencia" />
          <input id="km_referencia" name="km_referencia" type="number" step="any"
            v-model="form_quilometragem.km_referencia" class="form-control" />
          <InputError :message="form_quilometragem.errors.km_referencia" />
        </div>
        <div class="col">
          <InputLabel value="Mês de referência" for="mes_refencia" />
          <div class="row g-2">
            <div class="col">
              <input type="month" class="form-control" v-model="form_quilometragem.mes_referencia">
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-success">Enviar</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="accordion" id="accordion-example">
    <div v-for="group_data, index in veiculo.group_data_km?.grouped_data" :key="group_data" class="accordion-item">
      <h2 class="accordion-header" :id="'heading-' + index">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse-' + index"
          aria-expanded="false">
          {{ dateTimeFormat(index, { year: 'numeric', month: 'long' }) }} - total: {{
            parseFloat(group_data.total).toFixed(2) }}
        </button>
      </h2>
      <div :id="'collapse-' + index" class="accordion-collapse collapse" data-bs-parent="#accordion-example">
        <div class="accordion-body pt-0">
          <div class="table-responsive">
            <table class="table table-hover non-hover">
              <thead>
                <tr>
                  <th>Km de referência</th>
                  <th>Mês de referência</th>
                  <th>Açao</th>
                </tr>
              </thead>
              <tbody>
                <template v-for="km, indexkm in group_data" :key="km.id">
                  <tr v-if="indexkm != 'total'">
                    <td>{{ km.km_referencia }}</td>
                    <td>{{ dateTimeFormat(km.mes_referencia, { year: 'numeric', month: 'numeric' }) }}
                    </td>
                    <td>
                      <button @click="editarKm(km)" type="button" class="btn btn-primary me-2">
                        <IconPencil />
                      </button>
                      <button @click="destroyKm(km.id)" type="button" class="btn btn-danger">
                        <IconTrash />
                      </button>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
