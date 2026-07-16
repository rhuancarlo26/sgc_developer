<script setup>
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";
import { IconSquareCheck, IconX, IconDownload } from '@tabler/icons-vue';
import { dateTimeFormat } from '@/Utils/DateTimeUtils';

const props = defineProps({
	tipos: Array,
	contrato: [String, Number],
	produto: String,
})

const modalMapa = ref(null);

const title = ref('')
const modulo = ref({})

const abrirModal = (modulo_) => {

	title.value = `Campos do modulo ${modulo_.nome}`
	modulo.value = { ...modulo_ }

  	modalMapa.value.getBsModal().show();
}

const formatarTipo = (tipo) => {
	const tipo_ = props.tipos.find(item => item.value === tipo)
    return tipo_?.label ?? '-'
}

defineExpose({ abrirModal });
</script>

<template>
	<Modal ref="modalMapa" :title="title" modal-dialog-class="modal-xl">
		<template #body>

			<div class="d-flex justify-content-end mb-3">
				<a
					v-if="modulo.id && modulo.campos.length"
					:href="route('sgc.contratada.produtos.modulos.configuracoes.gerar-planilha-modelo', [
						props.contrato,
						props.produto,
						modulo.id,
					])"
					class="btn bg-gray-700" target="_blank"> 
					<IconDownload class="me-2" /> Gerar Planilha Modelo
				</a>
			</div>

			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="text-center">Nome do Campo</th>
							<th class="text-center">Tipo</th>
							<th class="text-center col-1">Obrigatório</th>
							<th class="text-center col-1">Limite</th>
							<th class="text-center col-1">Valor Mín</th>
							<th class="text-center col-1">Valor Máx</th>
							<th class="text-center col-1">Max Caracteres</th>
							<th class="text-center">Valor Exemplo</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(c, key) in modulo.campos" :key="key">
							<td class="text-center">{{ c.nome_campo }}</td>
							<td class="text-center">{{ formatarTipo(c.tipo) }}</td>
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
							<td class="text-center">
								<span v-if="c.tipo === 'data'">{{c.valor_exemplo}} OU {{dateTimeFormat(c.valor_exemplo)}} </span>
								<span v-else>{{ c.valor_exemplo }}</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

		</template>
	</Modal>
</template>
