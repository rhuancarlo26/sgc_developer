<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import Table from '@/Components/Table.vue';
import Breadcrumb from "@/Components/Breadcrumb.vue";
import ModelSearchForm from "@/Components/ModelSearchForm.vue";
import { format } from 'date-fns'
import { ptBR } from 'date-fns/locale'

defineProps({
    accesses: Object,
})

const formatarData = (data) => {
    if (!data) return '-'
    return format(new Date(data), 'dd/MM/yyyy HH:mm', { locale: ptBR })
}

const parseUserAgent = (ua) => {
    if (!ua) return '-'
    if (/mobile/i.test(ua)) return '📱 Mobile'
    if (/tablet/i.test(ua)) return '📲 Tablet'
    return '🖥️ Desktop'
}
</script>

<template>

    <Head title="Acessos > Usuários" />

    <AuthenticatedLayout>

        <template #header>
            <Breadcrumb :links="[
                { route: '#', label: 'Acessos' },
                { route: route('user-accesses.index'), label: 'Usuários' },
            ]" />
        </template>

        <div class="card card-body">

            <!-- Pesquisa -->
            <ModelSearchForm :search-columns="{
                'users.name': 'Nome',
                'users.email': 'Email',
                'ip_address': 'IP',
                'logged_in_at': 'Data de Login',
            }" />

            <!-- Listagem -->
            <Table :columns="['Usuário', 'E-mail', 'IP', 'Dispositivo', 'Data de Login']" :records="accesses"
                table-class="table-hover">
                <template #body="{ item }">
                    <tr>
                        <td>{{ item?.user?.name ?? '-' }}</td>
                        <td>{{ item?.user?.email ?? '-' }}</td>
                        <td class="font-monospace">{{ item?.ip_address ?? '-' }}</td>
                        <td>{{ parseUserAgent(item?.user_agent) }}</td>
                        <td>{{ formatarData(item?.logged_in_at) }}</td>
                    </tr>
                </template>
            </Table>

        </div>

    </AuthenticatedLayout>

</template>