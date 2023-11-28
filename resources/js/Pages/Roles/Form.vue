<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {withoutSpecialChars} from "@/Utils/StringUtils.js";
import {Head, useForm} from '@inertiajs/vue3';
import {IconTrash, IconDeviceFloppy} from "@tabler/icons-vue";
import InputError from "@/Components/InputError.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import {Link} from '@inertiajs/vue3';
import LinkConfirmation from "@/Components/LinkConfirmation.vue";

const props = defineProps({
    role: {
        type: Object,
    },
    routes: {
        type: Array
    }
});


const form = useForm({name: '', permissions: [], ...props.role})

if (props.role.permissions) {
    form.permissions = props.role.permissions.map(a => a.name);
}

const saveRole = () => {

    const formData = (data) => {
        return {id: data.id, name: data.name, permissions: data.permissions}
    }

    if (props.role.id) {

        form.transform(formData)
            .patch(route('cadastros.perfis.atualizar', props.role.id));

        return;
    }

    form.transform(formData)
        .post(route('cadastros.perfis.criar'));
}


const getRoutesSections = () => {

    return new Set(props.routes
        .map(r => {
            const prefix = r.action.prefix;

            if (!prefix) {
                return r.action.as ?? r.uri
            }

            return prefix.charAt(0) !== '/' ? prefix : prefix.replace('/', '');
        })
        .map(i => i.split('/')[0])
    );

}

const getPrefixesBySection = (section) => {

    const getPrefixName = (route) => {

        if (route.action.prefix) {
            const prefix = route.action.prefix;
            return prefix.charAt(0) !== '/' ? prefix : prefix.replace('/', '');
        }

        const uri = route.uri.charAt(0) !== '/' ? route.uri : route.uri.replace('/', '');
        return route.action.as ?? uri;
    }

    return new Set(props.routes
        .filter(r => getPrefixName(r).startsWith(section))
        .map(r => r.action.prefix ? r.action.prefix : (r.action.as ?? r.uri))
    );

}

const getRoutesByPrefix = (prefix) => {

    return new Set(props.routes
        .filter(r => {
            return [r.action.prefix, (r.action.as ?? r.uri)].includes(prefix);
        })
        .map(r => {
            return {alias: r.action.as ?? r.uri, ...r}
        })
    )

}

const togglePrefixRoutes = (prefix) => {

    const prefixRoutes = Array.from(getRoutesByPrefix(prefix)).map(p => p.alias);
    const alreadySelected = form.permissions.filter(p => prefixRoutes.includes(p)).length === prefixRoutes.length

    if (alreadySelected) {
        form.permissions = form.permissions.filter(p => !prefixRoutes.includes(p))
    } else {
        form.permissions = Array.from(new Set([...form.permissions, ...prefixRoutes]))
    }

}
</script>

<template>
    <Head title="Perfil"/>


    <AuthenticatedLayout>

        <template #header>
            <Breadcrumb :links="[
                    {route: '#', label: 'Cadastros'},
                    {route: route('cadastros.perfis.listagem'), label: 'Perfis'},
                    {route: route('cadastros.perfis.formulario', route().params), label: role.name ?? 'Formulário'}
                ]"/>
        </template>

        <form @submit.prevent="saveRole">

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="my-0">Nome</h3>
                </div>
                <div class="card-body">
                    <input class="form-control" placeholder="Nome do perfil" v-model="form.name"/>
                    <InputError :message="form.errors.name" class="mt-2"/>
                </div>
            </div>


            <div class="card mb-4">

                <div class="card-header">
                    <div>
                        <h3 class="my-0">Permissões</h3>
                        <small>As permissões selecionadas irão definir o que um usuário deste perfil poderá
                            acessar</small>
                        <InputError :message="form.errors.permissions" class="mt-2"/>
                    </div>

                </div>

                <div class="card-body bg-gray-500 space-y-4">
                    <!-- Seções de rotas (Primeiro nome antes do '.') -->
                    <template v-for="section in getRoutesSections()">

                        <div class="card">

                            <div class="card-header">
                                <h3 class="my-0">{{ section }}</h3>
                            </div>

                            <div class="card-body">
                                <div class="d-flex align-items-top prefix-row">

                                    <!--  Prefixos de rota (De acordo com o informado no web.php)-->
                                    <template v-for="prefix in getPrefixesBySection(section)">
                                        <div class="col prefix-col mx-2">


                                            <!-- Rotas (permissões do perfil) -->
                                            <table class="table table-striped table-bordered">
                                                <thead
                                                    v-show="withoutSpecialChars(prefix) !== withoutSpecialChars(section)">
                                                <tr class="table-secondary">
                                                    <td colspan="2" role="button" title="Selecionar tudo"
                                                        @click="togglePrefixRoutes(prefix)">
                                                        <h4 class="my-0">
                                                            {{ prefix }}
                                                        </h4>
                                                    </td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(route) in getRoutesByPrefix(prefix)"
                                                    :key="`${route.alias}`">
                                                    <td>
                                                        <label class="form-check-label"
                                                               :for="`checkRoute${route.alias}`">
                                                            {{ route.alias }}
                                                        </label>
                                                    </td>
                                                    <td class="w-1 text-center">
                                                        <input
                                                            name="permissions"
                                                            v-model="form.permissions"
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            :value="route.alias"
                                                            :id="`checkRoute${route.alias}`"
                                                        />
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>


                </div>

            </div>

            <!-- Ações -->
            <div class="card card-body">
                <div class="d-flex justify-content-end gap-2">

                    <LinkConfirmation
                        v-if="role.id"
                        v-slot="confirmation"
                        :options="{text: 'A remoção de um perfil afetará as permissões de todos os usuaŕios associados a ele' }">
                        <Link :onBefore="confirmation.show"
                              :href="route('cadastros.perfis.deletar', role.id)"
                              as="button"
                              method="delete"
                              type="button"
                              class="btn btn-danger">
                            <IconTrash class="me-2"/>
                            Deletar
                        </Link>
                    </LinkConfirmation>

                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <IconDeviceFloppy class="me-2"/>
                        Salvar
                    </button>
                </div>
            </div>

        </form>

    </AuthenticatedLayout>
</template>

<style scoped>

.prefix-row {
    overflow-x: scroll;
}

.prefix-col {
    min-width: 360px;

}

td[role="button"]:hover {
    color: var(--tblr-primary)
}
</style>
