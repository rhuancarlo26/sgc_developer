<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { ref } from 'vue';
import { computed, watch } from 'vue';

const props = defineProps({
  campanhas: { type: Array },
  modulos: { type: Array },
  grupo_faunisticos: { type: Array },
  form: { type: Object }
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
        <v-select :options="campanhas" label="id" :reduce="a => a.id" v-model="form.id_campanha">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_campanha" />
      </div>
      <div class="col">
        <InputLabel value="Selecione o Módulo" for="id_modulo" />
        <v-select :options="modulos" label="id" v-model="form.id_modulo">
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
        <select class="form-select" name="parcela_modulo" id="parcela_modulo" v-model="form.parcela_modulo">
          <option v-for="i in form.id_modulo?.tamanho_modulo" :key="i" :value="i">{{ i }}</option>
        </select>
        <InputError :message="form.errors.parcela_modulo" />
      </div>
      <div class="col">
        <InputLabel value="Selecione o ID da Armadilha" for="id_armadilha" />
        <v-select :options="form.id_modulo?.armadilhas" label="nome_id" v-model="form.id_armadilha">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_armadilha" />
      </div>
      <div class="col">
        <InputLabel value="Grupo Amostrado" for="id_grupo" />
        <v-select :options="grupo_faunisticos" label="grupo_faunistico" :reduce="a => a.id" v-model="form.id_grupo">
          <template #no-options="{}">
            Nenhum registro encontrado.
          </template>
        </v-select>
        <InputError :message="form.errors.id_grupo" />
      </div>
    </div>
  </form>
</template>