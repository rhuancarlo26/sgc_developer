<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import { IconSquareCheck, IconX, IconDownload } from '@tabler/icons-vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';

const modalErros = ref(null);

const errors = ref([])

const abrirModal = (errors_) => {
	errors.value = [...errors_]

  	modalErros.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
	<Modal ref="modalErros" title="Erros de importação" modal-dialog-class="modal-md">
		<template #body>

			<div class="card p-3 p-lg-4">
				<div class="group-errors">
					<ul>
						<li v-for="(item, index) in errors" :key="index" v-html="item"></li>
					</ul>
				</div>
			</div>

		</template>
	</Modal>
</template>

<style scoped>
.group-errors {
    max-height: 400px;
    overflow: auto;
}
</style>