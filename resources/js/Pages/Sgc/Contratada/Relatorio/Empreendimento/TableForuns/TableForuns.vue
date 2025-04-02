<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3'; // Importe o router do Inertia

const props = defineProps({
    contrato: Object,
    empreendimentos: Array,
    empreendimentos2: Object,
    estudos: Object,
    subprodutos: Object
});

const editingFields = ref({
    forum2025CGMAB: false,
    forum2025CGDESP: false,
});

const fieldValues = ref({
    forum2025CGMAB: props.empreendimentos2?.forum_2025_cgmab || '',
    forum2025CGDESP: props.empreendimentos2?.forum_2025_cgdesp || '',
});

const codEmp = props.empreendimentos2?.cod_emp;
const empreendimentoId = props.empreendimentos2?.id;

const empreendimentoFiltrado = computed(() => {
    return props.empreendimentos2 && props.empreendimentos2.cod_emp === codEmp ? props.empreendimentos2 : null;
});

const forum2024CGDesp = empreendimentoFiltrado.value?.forum_2024_cgdesp || 'N/A';
const forum2024CGMAB = empreendimentoFiltrado.value?.forum_2024_cgmab || 'N/A';
const forum2025CGDesp = ref(empreendimentoFiltrado.value?.forum_2025_cgdesp || '');
const forum2025CGMAB = ref(empreendimentoFiltrado.value?.forum_2025_cgmab || '');

const editarText = (field) => {
    for (let key in editingFields.value) {
        editingFields.value[key] = false;
    }
    editingFields.value[field] = true;
};

const saveField = (field) => {
    const data = {
        [field === 'forum2025CGMAB' ? 'forum_2025_cgmab' : 'forum_2025_cgdesp']: fieldValues.value[field]
    };

    router.post(route('sgc.contratada.empreendimento.updatecampo', { id: empreendimentoId }), data, {
        preserveState: true, 
        preserveScroll: true, 
        onSuccess: () => {

            if (field === 'forum2025CGMAB') {
                forum2025CGMAB.value = fieldValues.value.forum2025CGMAB;
            } else {
                forum2025CGDesp.value = fieldValues.value.forum2025CGDESP;
            }
            editingFields.value[field] = false;
  
            router.visit(window.location.href, {
                preserveState: true,
                preserveScroll: true,
            });
        },
        onError: (errors) => {
            console.error('Erro ao atualizar:', errors);
        }
    });
};

const cancelEdit = (field) => {
    editingFields.value[field] = false;
    fieldValues.value[field] = field === 'forum2025CGMAB' ? forum2025CGMAB.value : forum2025CGDesp.value;
};
</script>

<template>
    <div class="mt-2" style="background-color: white;">
        <div style="display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px;">
            <div style="flex: 0.5; border: 1px solid #ddd; padding: 10px; display: flex; align-items: center; justify-content: center;">
                <h3>CGMAB</h3>
            </div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;">
                <h3>2024</h3>
                <p>{{ forum2024CGMAB }}</p>
            </div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;">
                <h3>2025</h3>
                <p v-if="!editingFields.forum2025CGMAB">{{ forum2025CGMAB || 'N/A' }}</p>
                <div v-if="editingFields.forum2025CGMAB" class="mt-2">
                    <textarea 
                        v-model="fieldValues.forum2025CGMAB" 
                        class="w-100" 
                        name="forum" 
                        id="forum-cgmab"
                    ></textarea>
                    <div class="mt-2">
                        <button @click="saveField('forum2025CGMAB')" class="btn btn-success">Salvar</button>
                        <button @click="cancelEdit('forum2025CGMAB')" class="btn btn-secondary ml-2">Cancelar</button>
                    </div>
                </div>
                <button v-else @click="editarText('forum2025CGMAB')" class="btn btn-primary mt-2">Editar</button>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; gap: 20px;">
            <div style="flex: 0.5; border: 1px solid #ddd; padding: 10px; display: flex; align-items: center; justify-content: center;">
                <h3>CGDESP</h3>
            </div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;">
                <h3>2024</h3>
                <p>{{ forum2024CGDesp }}</p>
            </div>
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;">
                <h3>2025</h3>
                <p v-if="!editingFields.forum2025CGDESP">{{ forum2025CGDesp || 'N/A' }}</p>
                <div v-if="editingFields.forum2025CGDESP" class="mt-2">
                    <textarea 
                        v-model="fieldValues.forum2025CGDESP" 
                        class="w-100" 
                        name="forum" 
                        id="forum-cgdesp"
                    ></textarea>
                    <div class="mt-2">
                        <button @click="saveField('forum2025CGDESP')" class="btn btn-success">Salvar</button>
                        <button @click="cancelEdit('forum2025CGDESP')" class="btn btn-secondary ml-2">Cancelar</button>
                    </div>
                </div>
                <button v-else @click="editarText('forum2025CGDESP')" class="btn btn-primary mt-2">Editar</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.btn { padding: 5px 10px; margin-right: 5px; }
.btn-success { background-color: #28a745; border-color: #28a745; color: white; }
.btn-secondary { background-color: #6c757d; border-color: #6c757d; color: white; }
.btn-primary { background-color: #7ba6d4; border-color: #072749; color: white; }
.ml-2 { margin-left: 10px; }
.w-100 { width: 100%; }
</style>