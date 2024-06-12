<script setup>
import { Head } from "@inertiajs/vue3";
import { ref, defineProps } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import DocxModal from "./DocxModal.vue";
import { useForm } from '@inertiajs/vue3';


const props = defineProps({
    contrato: Object,
    comentarios: Object
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
const docxModal = ref();
const selectedItemId = ref(null);

const obterItens = async () => {
  try {
    const response = await fetch(route('sgc.relatorio_coordenacao.index'));
    const data = await response.json();
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


const abrirDoc = (idItem) => {
    selectedItemId.value = idItem;
    docxModal.value.abrirModal(idItem);
}

</script>

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
          <div class="d-flex flex-column gap-3">
            <div v-for="item in itens" :key="item.id" class="border p-3 rounded">
              <div>{{ item.nome_topico }}</div>

              <div class="container overflow-hidden">
                <div class="row gx-5">
                  <div class="col-sm-9">
                    <div class="input-group">
                        <input @input="form.arquivo = $event.target.files[0]"
                            type="file" class="form-control" id="inputGroupFile04"
                            aria-describedby="inputGroupFileAddon04"
                            aria-label="Upload">
                        <button @click="selecionarItem(item.id_item);
                            salvarAnexo()" class="btn btn-success"
                            type="button"
                            id="inputGroupFileAddon04">Salvar
                        </button>
                    </div>
                  </div>
                  <div class="col d-flex flex-column gap-2">
                    <button class="btn btn-primary" @click="abrirDoc(item.id_item)">Abrir</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </Navbar>
      <DocxModal ref="docxModal" href="javascript:void(0)" :itemId="selectedItemId" :comentarios="props.comentarios"/>
    </AuthenticatedLayout>
  </div>
</template>

<style>

</style>

