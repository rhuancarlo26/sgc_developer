<script setup>
import { defineProps } from 'vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { router, Head, useForm } from '@inertiajs/vue3';
import Navbar from '../Navbar.vue';
import Swal from 'sweetalert2';
import { useToast } from "vue-toastification";
const toast = useToast();

const props = defineProps({
  davs: Object,
  contrato: Object,
});

const aprovarDav = (id) => {
  Swal.fire({
        title: "Aprovar",
        text: "Deseja continuar a aprovação dessa DAV?",
        icon: "warning",
        showCloseButton: true,
        showCancelButton: true,
        focusConfirm: false,
  }).then((r) => {
      if (r.isConfirmed) {
        router.put(route('sgc.gestao.aprovarDav', { id: id }))
        toast.success('Dav reaprovada com sucesso!');
      }
  })
  // router.put(route('sgc.gestao.aprovarDav', { id: id }));
  // 'sgc.gestao.aprovarDav', { id: props.dav.id }
};
</script>
<template>
  <Head :title="`${contrato.contratada.slice(0, 10)}...`" />
  <AuthenticatedLayout>
    <template #header>
      <div class="w-100 d-flex justify-content-between">
        <Breadcrumb
          class="align-self-center"
          :links="[
            { route: route('sgc.gestao.listagem', contrato.tipo_contrato), label: `Gestão de Contratos` },
            { route: '#', label: contrato.contratada }
          ]"
        />
      </div>
      </template>
      <Navbar :tipo="contrato">
        <template #body>
          <div>
            <h1>Histórico de DAVs reprovadas</h1>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>DAV</th>
                  <th>Usuário</th>
                  <th>Data</th>
                  <th>Status Anterior</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="dav in davs" :key="dav.id">
                  <td>{{ dav.id }}</td>
                  <td><a :href="'/sgc/gestao/dav/'">{{ dav.dav_id }}</a></td>
                  <td>{{ dav.user_id }}</td>
                  <td>{{ new Date(dav.created_at).toLocaleDateString() }}</td>
                  <td>{{ dav.status_anterior }}</td>
                  <td>
                    <button v-if="dav.status_novo === 'reprovado'" @click="aprovarDav(dav.dav_id)">
                      Aprovar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </template>
      </Navbar>
  </AuthenticatedLayout>
</template>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}
th {
  background-color: #f4f4f4;
}
button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}
button:hover {
  background-color: #45a049;
}
</style>
