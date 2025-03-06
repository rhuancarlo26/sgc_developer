<script setup>
import { ref, computed } from 'vue';

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

const codEmp = props.empreendimentos2?.cod_emp;

const empreendimentoFiltrado = computed(() => {
    return props.empreendimentos2 && props.empreendimentos2.cod_emp === codEmp ? props.empreendimentos2 : null;
});

const forum2024CGDesp = empreendimentoFiltrado.value?.forum_2024_cgdesp || 'N/A';
const forum2024CGMAB = empreendimentoFiltrado.value?.forum_2024_cgmab || 'N/A';

const editarText = (field) => {
    for (let key in editingFields.value) {
        editingFields.value[key] = false;
    }
    editingFields.value[field] = true;
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
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;" @click="editarText('forum2025CGMAB')">
                <h3>2025</h3>
                <div v-if="editingFields.forum2025CGMAB" class="mt-2">
                    <textarea class="w-100" name="forum" id="forum-cgmab"></textarea>
                </div>
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
            <div style="flex: 1; border: 1px solid #ddd; padding: 10px; text-align: center;" @click="editarText('forum2025CGDESP')">
                <h3>2025</h3>
                <div v-if="editingFields.forum2025CGDESP" class="mt-2">
                    <textarea class="w-100" name="forum" id="forum-cgdesp"></textarea>
                </div>
            </div>
        </div>
      
    </div>
</template>
