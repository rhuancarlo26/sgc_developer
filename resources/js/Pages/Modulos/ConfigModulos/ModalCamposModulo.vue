<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import { IconSquareCheck, IconX } from '@tabler/icons-vue';

const modalMapa = ref(null);

const title = ref('')
const campos = ref([])

const abrirModal = (modulo) => {
	console.log(modulo)

	title.value = `Campos do modulo ${modulo.nome}`
	campos.value = [...modulo.campos]

  	modalMapa.value.getBsModal().show();
}

defineExpose({ abrirModal });
</script>

<template>
	<Modal ref="modalMapa" :title="title" modal-dialog-class="modal-xl">
		<template #body>

			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="text-center">Nome do Campo</th>
							<th class="text-center">Tipo</th>
							<th class="text-center col-1">Obrigatório</th>
							<th class="text-center col-1">Regra</th>
							<th class="text-center col-1">Valor Mín</th>
							<th class="text-center col-1">Valor Máx</th>
							<th class="text-center col-1">Max Caracteres</th>
							<th class="text-center">Valor Exemplo</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(c, key) in campos" :key="key">
							<td class="text-center">{{ c.nome_campo }}</td>
							<td class="text-center">{{ c.tipo }}</td>
							<td class="text-center">
								<IconSquareCheck v-if="c.obrigatorio" class="text-green" />
								<IconX v-else class="text-danger" />
							</td>
							<td class="text-center">
								<IconSquareCheck v-if="c.regra" class="text-green" />
								<IconX v-else class="text-danger" />
							</td>
							<td class="text-center">{{ c.valor_min }}</td>
							<td class="text-center">{{ c.valor_max }}</td>
							<td class="text-center">{{ c.max_caracteres }}</td>
							<td class="text-center">{{ c.valor_exemplo }}</td>
						</tr>
					</tbody>
				</table>
			</div>

		</template>
	</Modal>
</template>