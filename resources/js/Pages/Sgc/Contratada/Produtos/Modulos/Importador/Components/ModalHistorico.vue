<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import { IconPencil, IconExclamationMark, IconX, IconCheck } from '@tabler/icons-vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';
import axios from "axios";

// import { colorStatus } from '@/Utils/ImportadorUtils';

const modalHistorico = ref(null);

const historico = ref([])

const abrirModal = () => {
	modalHistorico.value.getBsModal().show();

	buscarHistoricoImportacao()
}

const buscarHistoricoImportacao = () => {
	historico.value = []
    axios.get(route('modulos.importador.buscarHistorico', [route().params.importador]))
		.then(resp => {
			historico.value = resp.data?.historicos ?? []
		})
}

const componentIcon = {
	1: {
		color: colorStatus(1),
		icon: 'IconPencil',
	},
	2: {
		color: colorStatus(2),
		icon: 'IconExclamationMark',
	},
	3: {
		color: colorStatus(3),
		icon: 'IconX',
	},
	4: {
		color: colorStatus(4),
		icon: 'IconCheck',
	},
}

defineExpose({ abrirModal });
</script>

<template>
	<Modal ref="modalHistorico" title="Histórico de Pareceres" modal-dialog-class="modal-lg">
		<template #body>

			<div class="d-flex flex-column gap-2">

				<div v-for="h in historico" :key="h.id" class="d-flex gap-3">
					<button class="btn btn-status" :class="[
						`btn-${colorStatus(h.status)}`, 
					]">
						<!-- [1,3].includes(h.status) ? 'order-1' : 'order-2' -->
						<IconPencil v-if="h.status == 1" />
						<IconExclamationMark v-else-if="h.status == 2" />
						<IconX v-else-if="h.status == 3" />
						<IconCheck v-else-if="h.status == 4" />
					</button>

					<div class="order-1 d-flex flex-column gap-2 border border-1 rounded p-2 flex-grow-1">
						
						<span>
							Status: <strong>{{ h.status_historico_formatado }}</strong>
						</span>

						<div class="mb-2">
							{{ h.usuario?.name }} - {{ h.created_at }}
						</div>

						<div v-if="h.parecer" class="parecer">
							<strong>Parecer {{h.status == 2 ? 'Técnico (Empresa)' : 'da Análise (Fiscal)'}}:</strong>
							<br>
							<div class="border border-1 rounded p-2" v-html="h.parecer"></div>
						</div>
					</div>
				</div>
			</div>
		</template>

		<template #footer>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Fechar">Fechar</button>
		</template>
	</Modal>
</template>

<style scoped>
    .btn-status {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        padding: 0;
        font-size: 20px;
        /* color: white; */
    }
</style>