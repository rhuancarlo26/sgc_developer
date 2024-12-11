<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  contrato: { type: Object },
  servico: { type: Object }
});

const form = useForm({
  id: null,
  id_servico: null,
  nome_id: null,
  id_campanha: null,
  id_modulo: null,
  parcela_modulo: null,
  id_armadilha: null,
  id_grupo: null
});
</script>
<template>
  <form>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="ID Registro" for="nome_id" />
        <input type="text" class="form-control" :value="form.nome_id" disabled>
        <InputError :message="form.errors.nome_id" />
      </div>
      <div class="col">
        <InputLabel value="Selecione a Campanha" for="id_campanha" />
        <v-select :options="[]" label="id" :reduce="a => a.id" v-model="form.id_campanha">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_campanha" />
      </div>
      <div class="col">
        <InputLabel value="Selecione o Módulo" for="id_modulo" />
        <v-select :options="[]" label="id" :reduce="a => a.id" v-model="form.id_modulo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_modulo" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <InputLabel value="Selecione a Parcela do Módulo Amostral" for="parcela_modulo" />
        <v-select :options="[]" label="id" :reduce="a => a.id" v-model="form.parcela_modulo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.parcela_modulo" />
      </div>
      <div class="col">
        <InputLabel value="Selecione o ID da Armadilha" for="id_armadilha" />
        <v-select :options="[]" label="id" :reduce="a => a.id" v-model="form.id_armadilha">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_armadilha" />
      </div>
      <div class="col">
        <InputLabel value="Grupo Amostrado" for="id_grupo" />
        <v-select :options="[]" label="id" :reduce="a => a.id" v-model="form.id_grupo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_grupo" />
      </div>
    </div>
  </form>
</template>