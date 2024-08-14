<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import Navbar from "../../Navbar.vue";
import {Head, Link, router, useForm} from "@inertiajs/vue3";
import ModelSearchFormAllColumns from "@/Components/ModelSearchFormAllColumns.vue";
import Table from "@/Components/Table.vue";
import NavButton from "@/Components/NavButton.vue";
import LinkConfirmation from "@/Components/LinkConfirmation.vue";
import {ref} from "vue";
import {IconEye} from "@tabler/icons-vue";
import {IconPlus} from "@tabler/icons-vue";
import {IconPencil} from "@tabler/icons-vue";
import {IconTrash} from "@tabler/icons-vue";
import NavLink from "@/Components/NavLink.vue";
import {IconMap} from "@tabler/icons-vue";
import {dateTimeFormat} from "@/Utils/DateTimeUtils";
import Modal from "@/Components/Modal.vue";
import {usePage} from "@inertiajs/vue3";
import {IconDeviceFloppy} from "@tabler/icons-vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const modalVisualizarACA = ref();
const aca = ref({});

const abrirModal = (item) => {
    aca.value = item;

    modalVisualizarACA.value.getBsModal().show();
}

defineExpose({abrirModal});
</script>

<template>
    <Modal ref="modalVisualizarACA" title="Atestado de Conformidade Ambiental"
           modal-dialog-class="modal-xl">
        <template #body>

        </template>
        <template #footer>
        </template>
    </Modal>
</template>
