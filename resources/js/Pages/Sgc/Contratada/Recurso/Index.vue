<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import DocxModal from "./DocxModal.vue";

import InputError from '@/Components/InputError.vue';
import { router, useForm } from '@inertiajs/vue3';
import { IconDots } from '@tabler/icons-vue';


const props = defineProps({
  contrato: Object
})

const form = useForm({
  id: null,
  contrato_id: props.contrato.id,
  caminho: null,
  arquivo: null,
  versao: null,
  item_id: null, 
  descricao: null
})

const itens = ref([]);

const obterItens = async () => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.index'));
    const data = await response.json();
    console.log('Itens obtidos:', data); 
    
    let item = null;

    if (data.length > 0) {
      
      item = data[7];

      form.item_id = item.id_item;
    }

    itens.value = data;
  } catch (error) {
    console.error('Erro ao obter os itens:', error);
  }
}
obterItens();

const selecionarItem = (idItem) => {  
  form.item_id = idItem;
}


const salvarAnexo = () => {
  if (form.id) {
    form.post(route('sgc.contratada.store_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  } else {
    form.post(route('sgc.contratada.store_anexo'), {
      onSuccess: () => {
        form.reset();
        document.getElementById('inputfile').value = null;
      }
    });
  }
}

const docxModal = ref();

const abrirDoc = (idItem) => {
    docxModal.value.abrirModal(idItem);
}

</script>
<style>
.lista-itens {
  display: flex;
  flex-direction: column;
  gap: 10px; 
}

.item {
  border: 1px solid #a09e9e; 
  padding: 10px; 
  border-radius: 5px; 
}

.button{
  border: 1px solid #2f2a3f;
  position: absolute; 
  z-index: 1;
  border-radius: 5px;  
  right: 03%;
}


</style>
<template>
  <div>
    <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
    <AuthenticatedLayout>
      <template #header>
        <div class="w-100 d-flex justify-content-between">
          <Breadcrumb class="align-self-center" :links="[
            { route: route('contratos.gestao.listagem', contrato.tipo_id), label: `Gestão de Contratos` },
            { route: '#', label: contrato.contratada }
          ]
            " />
        </div>
      </template>

    <Navbar :contrato="contrato">
        <template #body>
            <div class="lista-itens">
              <div v-for="item in itens" :key="item.id" class="item">
                {{ item.id_item }} - {{ item.descricao }}
                <button class="button" @click="abrirDoc(item.id_item)">Abrir</button>

                <input @input="form.arquivo = $event.target.files[0]" id="inputfile" type="file" class="form-control">
                <div class="row g-2">
                  <div class="col">
                    <input v-model="form.descricao" type="text" class="form-control">
                    <InputError :message="form.errors.descricao" />
                  </div>
                  <div class="col-auto">
                    <button class="button" @click="selecionarItem(item.id_item); salvarAnexo()">Salvar</button>
                  </div>
                </div>
              </div>
            </div>
            
        </template>
    </Navbar>
    <DocxModal ref="docxModal" href="javascript:void(0)"/>
    </AuthenticatedLayout>
  </div>
  
</template>