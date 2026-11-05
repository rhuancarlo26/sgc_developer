<script setup>
import { computed, ref } from 'vue';
import { IconPaperclip, IconTrash, IconPlus } from '@tabler/icons-vue';
import { agruparAnexos, classeAnexo } from './anexos';

const props = defineProps({ form: { type: Object, required: true }, uploading: Boolean });
const tituloNovo = ref('');
const erroBloco = ref('');
const erroArquivos = ref('');
const blocosExtras = ref([]);
const bloqueado = computed(() => props.form.processing || props.uploading);
const grupos = computed(() => {
    const lista = agruparAnexos(props.form.anexos);
    for (const bloco of blocosExtras.value) {
        if (!lista.some(item => item.id === bloco.id)) lista.push({ ...bloco, arquivos: [] });
    }
    return lista;
});

function adicionarBloco() {
    const titulo = tituloNovo.value.trim();
    erroBloco.value = '';
    if (!titulo) { erroBloco.value = 'Informe o nome do bloco.'; return; }
    if (grupos.value.some(item => item.titulo.toLocaleLowerCase('pt-BR') === titulo.toLocaleLowerCase('pt-BR'))) {
        erroBloco.value = 'Já existe um bloco com esse nome.';
        return;
    }
    blocosExtras.value.push({ id: `outros_${crypto.randomUUID()}`, titulo });
    tituloNovo.value = '';
}

function selecionarArquivos(grupo, event) {
    erroArquivos.value = '';
    const arquivos = Array.from(event.target.files || []);
    const novos = [];
    for (const arquivo of arquivos) {
        if (arquivo.size > 20 * 1024 * 1024) {
            erroArquivos.value = 'Cada arquivo deve ter no máximo 20 MB.';
            continue;
        }
        const duplicado = grupo.arquivos.some(item => item.arquivo?.name === arquivo.name
            && item.arquivo?.size === arquivo.size && item.arquivo?.lastModified === arquivo.lastModified);
        if (!duplicado) novos.push({ arquivo, classe: grupo.id, titulo_bloco: grupo.titulo });
    }
    if (props.form.anexos.filter(item => !item.remover).length + novos.length > 100) {
        erroArquivos.value = 'O limite é de 100 anexos por campanha.';
    } else {
        props.form.anexos.push(...novos);
    }
    event.target.value = '';
}

function removerArquivo(anexo) {
    if (anexo.id) anexo.remover = true;
    else props.form.anexos.splice(props.form.anexos.indexOf(anexo), 1);
}

function erroAnexo(anexo) {
    const indice = props.form.anexos.indexOf(anexo);
    return props.form.errors?.[`anexos.${indice}.arquivo`] || props.form.errors?.[`anexos.${indice}.classe`]
        || props.form.errors?.[`anexos.${indice}.titulo_bloco`];
}

function validarCampos() {
    return props.form.anexos.some(item => !item.remover && !item.id && !item.arquivo) ? 1 : 0;
}
defineExpose({ validarCampos });
</script>

<template>
    <section class="card w-100">
        <div class="card-header"><h3 class="my-0">Anexos</h3></div>
        <div class="card-body">
            <p class="text-muted mb-3">Organize os documentos por classe. Você pode selecionar vários arquivos de uma vez ou adicionar mais depois. Até 20 MB por arquivo.</p>
            <div v-if="uploading" class="alert alert-info" role="status" aria-live="polite">
                Enviando anexos: {{ form.anexos.filter(item => !item.id && item.upload_token && !item.remover).length }}
                de {{ form.anexos.filter(item => !item.id && !item.remover).length }} recebidos.
                A campanha será salva ao concluir o envio.
            </div>
            <div v-if="erroArquivos || form.errors?.anexos" class="alert alert-danger" role="alert">{{ erroArquivos || form.errors.anexos }}</div>
            <div class="anexos-grid">
                <section v-for="(grupo, indice) in grupos" :key="grupo.id" class="anexo-bloco">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                        <h4 class="mb-0 bloco-titulo"><span class="bloco-numero">{{ indice + 1 }}</span>{{ grupo.titulo }}</h4>
                        <span class="badge bg-light text-secondary">{{ grupo.arquivos.length }} arquivo{{ grupo.arquivos.length === 1 ? '' : 's' }}</span>
                    </div>
                    <ul v-if="grupo.arquivos.length" class="list-unstyled mb-3">
                        <li v-for="anexo in grupo.arquivos" :key="anexo.id || form.anexos.indexOf(anexo)" class="arquivo-item">
                            <div class="flex-grow-1 arquivo-nome">
                                <a v-if="anexo.url" :href="anexo.url" target="_blank" rel="noopener">{{ anexo.nome_arquivo }}</a>
                                <span v-else>{{ anexo.arquivo?.name || anexo.nome_arquivo }}</span>
                                <small class="d-block" :class="anexo.upload_status === 'erro' ? 'text-danger' : 'text-muted'">
                                    {{ anexo.id ? 'Salvo' : anexo.upload_status === 'enviando' ? `Enviando: ${anexo.upload_progresso}%` : anexo.upload_token ? 'Recebido — aguardando salvar a campanha' : 'A enviar' }}
                                </small>
                                <div v-if="anexo.upload_status === 'enviando'" class="progress mt-1" role="progressbar" :aria-valuenow="anexo.upload_progresso" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso do envio"><div class="progress-bar" :style="{ width: `${anexo.upload_progresso}%` }"></div></div>
                                <small v-if="anexo.upload_erro" class="text-danger d-block" role="alert">{{ anexo.upload_erro }}</small>
                                <small v-if="erroAnexo(anexo)" class="text-danger d-block">{{ erroAnexo(anexo) }}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" :disabled="bloqueado" :aria-label="`Remover ${anexo.arquivo?.name || anexo.nome_arquivo}`" @click="removerArquivo(anexo)"><IconTrash :size="16" /></button>
                        </li>
                    </ul>
                    <p v-else class="text-muted small mb-3">Nenhum arquivo adicionado.</p>
                    <label :for="`anexos-${grupo.id}`" class="form-label small"><IconPaperclip :size="16" class="me-1" />Adicionar arquivos em {{ grupo.titulo }}</label>
                    <input :id="`anexos-${grupo.id}`" type="file" multiple class="form-control form-control-sm" :disabled="bloqueado" @change="selecionarArquivos(grupo, $event)">
                    <div v-for="anexo in form.anexos.filter(item => item.remover && classeAnexo(item) === grupo.id)" :key="`removido-${anexo.id}`" class="small text-muted mt-2">
                        {{ anexo.nome_arquivo }} — será removido ao salvar.
                        <button type="button" class="btn btn-link btn-sm p-0" :disabled="bloqueado" @click="anexo.remover = false">Desfazer</button>
                    </div>
                </section>
            </div>
            <div class="novo-bloco mt-3">
                <label for="novo-bloco-anexos" class="form-label">Adicionar outro bloco</label>
                <div class="d-flex flex-wrap gap-2">
                    <input id="novo-bloco-anexos" v-model="tituloNovo" type="text" maxlength="100" placeholder="Ex.: Outros" class="form-control nome-bloco" :disabled="bloqueado" @keydown.enter.prevent="adicionarBloco">
                    <button type="button" class="btn btn-outline-primary" :disabled="bloqueado" @click="adicionarBloco"><IconPlus :size="16" class="me-1" />Adicionar bloco</button>
                </div>
                <small v-if="erroBloco" class="text-danger d-block mt-1">{{ erroBloco }}</small>
                <small class="text-muted d-block mt-2">Adicione arquivos para manter o bloco personalizado ao salvar a campanha.</small>
            </div>
        </div>
    </section>
</template>

<style scoped>
.anexos-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.anexo-bloco { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; }
.bloco-titulo { display: flex; align-items: center; gap: 8px; font-size: .95rem; font-weight: 600; }
.bloco-numero { display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; flex-shrink: 0; border-radius: 6px; background: #eff6ff; color: #2563eb; font-size: .8rem; }
.arquivo-item { display: flex; align-items: center; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.arquivo-nome { min-width: 0; overflow-wrap: anywhere; }
.novo-bloco { border-top: 1px solid #e2e8f0; padding-top: 16px; }
.nome-bloco { flex: 1 1 200px; max-width: 360px; }
@media (max-width: 767px) { .anexos-grid { grid-template-columns: 1fr; } }
</style>
