<script setup>
import { computed, ref, onBeforeUnmount } from 'vue';
import { agruparAnexos } from './anexos';
const props = defineProps({ anexos: { type: Array, default: () => [] } });
const grupos = computed(() => agruparAnexos(props.anexos).filter(grupo => grupo.arquivos.length));
const modalPdf = ref(null);
const pdfSelecionado = ref(null);
let overflowAnterior;

const ehPdf = anexo => anexo.mime_type === 'application/pdf'
    || /\.pdf$/i.test(anexo.nome_arquivo || '')
    || /\.pdf(?:[?#]|$)/i.test(anexo.url || '');

function abrirAnexo(anexo, event) {
    if (!ehPdf(anexo) || !anexo.url || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    pdfSelecionado.value = anexo;
    overflowAnterior = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    modalPdf.value.showModal();
}

function limparPdf() {
    pdfSelecionado.value = null;
    if (overflowAnterior !== undefined) {
        document.body.style.overflow = overflowAnterior;
        overflowAnterior = undefined;
    }
}

function fecharFora(event) {
    if (event.target !== modalPdf.value) return;
    const area = modalPdf.value.getBoundingClientRect();
    if (event.clientX < area.left || event.clientX > area.right || event.clientY < area.top || event.clientY > area.bottom) {
        modalPdf.value.close();
    }
}
onBeforeUnmount(limparPdf);
</script>

<template>
    <section class="mt-4">
        <h4 class="section-title">Anexos</h4>
        <div v-if="grupos.length" class="anexos-grid">
            <div v-for="grupo in grupos" :key="grupo.id" class="anexo-grupo">
                <h5>{{ grupo.titulo }} <span class="badge bg-light text-secondary ms-1">{{ grupo.arquivos.length }}</span></h5>
                <a v-for="anexo in grupo.arquivos" :key="anexo.id" :href="anexo.url" target="_blank" rel="noopener" class="arquivo-link" @click="abrirAnexo(anexo, $event)"><span>{{ anexo.nome_arquivo || 'Anexo' }}</span><span class="text-primary ms-2">{{ ehPdf(anexo) ? 'Visualizar' : 'Abrir' }}</span></a>
            </div>
        </div>
        <div v-else class="empty-state">Nenhum anexo vinculado.</div>
    </section>
    <Teleport to="body">
        <dialog ref="modalPdf" class="pdf-modal" aria-labelledby="titulo-anexo-pdf" @close="limparPdf" @click="fecharFora">
            <div class="pdf-modal-header">
                <h5 id="titulo-anexo-pdf" class="mb-0">{{ pdfSelecionado?.nome_arquivo || 'Visualizar PDF' }}</h5>
                <button type="button" class="btn-close flex-shrink-0" aria-label="Fechar PDF" autofocus @click="modalPdf.close()"></button>
            </div>
            <iframe v-if="pdfSelecionado" :src="pdfSelecionado.url" :title="pdfSelecionado.nome_arquivo || 'Documento PDF'" class="pdf-preview"></iframe>
            <div class="pdf-modal-footer">
                <span class="text-muted small">Se o PDF não aparecer, use o link ao lado.</span>
                <a v-if="pdfSelecionado" :href="pdfSelecionado.url" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">Abrir em nova aba</a>
            </div>
        </dialog>
    </Teleport>
</template>

<style scoped>
.section-title { font-size: 1.05rem; font-weight: 700; margin-bottom: 12px; }
.anexos-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
.anexo-grupo { border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; }
.anexo-grupo h5 { font-size: .95rem; margin-bottom: 12px; }
.arquivo-link { display: flex; justify-content: space-between; padding: 8px 0; color: inherit; text-decoration: none; border-top: 1px solid #f1f5f9; overflow-wrap: anywhere; }
.empty-state { border: 1px dashed #c9ced3; border-radius: 6px; color: #6c757d; padding: 16px; text-align: center; }
.pdf-modal { width: min(1100px, 95vw); height: 90vh; max-width: 95vw; max-height: 95vh; padding: 0; border: 0; border-radius: 10px; box-shadow: 0 16px 48px rgba(15, 23, 42, .25); }
.pdf-modal[open] { display: flex; flex-direction: column; }
.pdf-modal::backdrop { background: rgba(15, 23, 42, .6); }
.pdf-modal-header, .pdf-modal-footer { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 14px 18px; }
.pdf-modal-header { border-bottom: 1px solid #e2e8f0; }
.pdf-modal-header h5 { overflow-wrap: anywhere; min-width: 0; font-size: 1rem; }
.pdf-modal-footer { border-top: 1px solid #e2e8f0; flex-wrap: wrap; }
.pdf-preview { flex: 1; min-height: 0; width: 100%; border: 0; background: #f1f5f9; }
@media (max-width: 767px) { .anexos-grid { grid-template-columns: 1fr; } }
</style>
