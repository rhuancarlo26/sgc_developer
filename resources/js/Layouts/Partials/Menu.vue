<script setup>

import { 
    IconClipboardList, 
    IconLayoutDashboard, 
    IconFileCheck, 
    IconFileCertificate, 
    IconWorld 
} from "@tabler/icons-vue"

import NavLink from "@/Components/NavLink.vue";
import NavDropdown from "@/Components/NavDropdown.vue";
import NavDropdownLink from "@/Components/NavDropdownLink.vue";

</script>

<template>
    <header class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar">
                <div class="container-xl">
                    <div class="row flex-fill align-items-center">
                        <div class="col">
                            <ul class="navbar-nav">

                                <NavLink
                                    route-name="dashboard"
                                    title="Dashboard"
                                    :icon="IconLayoutDashboard" />

                                <!-- Contratos -->
                                <NavLink 
                                    route-name="contratos.gestao.listagem" 
                                    title="Contratos" 
                                    :icon="IconFileCertificate" 
                                />

                                <!-- Licenças -->
                                <NavLink
                                    route-name="licencas"
                                    title="Licenças"
                                    :icon="IconFileCheck" />

                                <!-- Ambiente Geo -->
                                <NavLink
                                    route-name="ambienteGeo"
                                    title="Ambiente Geo"
                                    :icon="IconWorld" />

                                <!-- Cadastros -->
                                <NavDropdown
                                    prefix="cadastros.*"
                                    title="Cadastros"
                                    :icon="IconClipboardList">

                                    <!-- Usuários -->
                                    <NavDropdownLink
                                        route-name="cadastros.usuarios.listagem"
                                        active-on-route-prefix="cadastros.usuarios.*"
                                        title="Usuários" />

                                    <!-- Perfis -->
                                    <NavDropdownLink
                                        route-name="cadastros.perfis.listagem"
                                        active-on-route-prefix="cadastros.perfis.*"
                                        title="Perfis" />


                                </NavDropdown>

                                <!-- Dashboard -->
                                <NavLink route-name="contratos.gestao.listagem" active-on-route-prefix="contratos.*"
                                    title="Contratos" :icon="IconLayoutDashboard" />

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
