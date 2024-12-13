<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object },
  form: { type: Object }
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Sexo" for="sexo" />
        <v-select :options="['Macho', 'Fêmea', 'Indefinido']" name="sexo" id="sexo" v-model="form.sexo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.sexo" />
      </div>
      <div class="col">
        <InputLabel value="Faixa etária" for="faixa_etaria" />
        <v-select :options="['Jovem', 'Adulto', 'Indeterminado']" name="faixa_etaria" id="faixa_etaria"
          v-model="form.faixa_etaria">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.faixa_etaria" />
      </div>
      <div class="col">
        <InputLabel value="Marcação" for="marcacao" />
        <input type="text" class="form-control" name="marcacao" id="marcacao" v-model="form.marcacao">
        <InputError :message="form.errors.marcacao" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="N° da Marcação" for="num_marcacao" />
        <input type="text" class="form-control" name="num_marcacao" id="num_marcacao" v-model="form.num_marcacao">
        <InputError :message="form.errors.num_marcacao" />
      </div>
      <div class="col">
        <InputLabel value="Data do Registro" for="data_registro" />
        <input type="date" class="form-control" name="data_registro" id="data_registro" v-model="form.data_registro">
        <InputError :message="form.errors.data_registro" />
      </div>
      <div class="col">
        <InputLabel value="Hora do Registro" for="hora_registro" />
        <input type="time" class="form-control" name="hora_registro" id="hora_registro" v-model="form.hora_registro">
        <InputError :message="form.errors.hora_registro" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Coletado" for="coletado" />
        <v-select :options="['Sim', 'Não']" name="coletado" id="coletado" v-model="form.coletado">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.coletado" />
      </div>
      <div class="col">
        <InputLabel value="N° tombamento ou registro de entrada na coleção" for="num_tombamento" />
        <input type="text" class="form-control" name="num_tombamento" id="num_tombamento" v-model="form.num_tombamento">
        <InputError :message="form.errors.num_tombamento" />
      </div>
      <div class="col">
        <InputLabel value="Dados biométricos anotados" for="dds_bio_anotados" />
        <v-select :options="['Sim', 'Não']" name="dds_bio_anotados" id="dds_bio_anotados"
          v-model="form.dds_bio_anotados">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.dds_bio_anotados" />
      </div>
      <div class="col">
        <InputLabel value="N° de indivíduos do registro" for="num_individuos" />
        <input type="text" class="form-control" name="num_individuos" id="num_individuos" v-model="form.num_individuos">
        <InputError :message="form.errors.num_individuos" />
      </div>
    </div>
  </form>
</template>