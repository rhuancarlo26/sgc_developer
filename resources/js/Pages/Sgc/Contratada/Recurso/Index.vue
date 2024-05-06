<script setup>
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../Navbar.vue";
import { renderAsync } from 'docx-preview';
import DocxModal from "./DocxModal.vue";

defineProps({
  contrato: Object,
});

const docxModal = ref();

const abrirDoc = () => {
    docxModal.value.abrirModal();
}
// const wordDocument = ref(null);
// const teste = ref(null)

// const styleContainer = document.createElement('style');
// styleContainer.innerHTML = `
//     .docx-wrapper {
//         background-color: red;
//     }
// `;

// console.log(styleContainer)

// onMounted(async () => {
//   try {
//     const filePath = new URL('./teste.docx', import.meta.url);
//     const response = await fetch(filePath);
//     if (!response.ok) {
//       throw new Error('Erro ao carregar o documento do Word');
//     }
//     const wordBlob = await response.blob({ type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });

//     wordDocument.value = wordBlob;

//     renderAsync(wordDocument.value, teste.value)

//     if (teste.value) {

//     } else {
//       console.error('Elemento #wordViewer não encontrado.');
//     }
//   } catch (error) {
//     console.error('Erro ao carregar o documento do Word:', error);
//     // Lidar com o erro, se necessário
//   }
// });
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
            <div>
                <button @click="abrirDoc()">teste</button>
            </div>
            <!-- <div ref="teste">
                teste
            </div> -->
        </template>
    </Navbar>
    <DocxModal ref="docxModal" href="javascript:void(0)"/>
    </AuthenticatedLayout>
  </div>
</template>
