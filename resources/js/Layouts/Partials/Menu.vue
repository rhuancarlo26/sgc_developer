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

                                <!-- Dashboard -->
                                <NavLink route-name="dashboard" title="Dashboard" :icon="IconLayoutDashboard" />

                                <!-- Contratos -->
                                <NavDropdown prefix="contratos.*" title="Contratos" :icon="IconLayoutDashboard">

                                    <!-- Gestão Ambiental -->
                                    <NavDropdownLink route-name="contratos.gestao.listagem" param-name="tipo"
                                        route-param="1" active-on-route-prefix="contratos.gestao.*"
                                        title="Gestão Ambiental" />

                                    <!-- Estudo Ambiental -->
                                    <NavDropdownLink route-name="sgc.gestao.dashboard.index" param-name="tipo"
                                        route-param="2" active-on-route-prefix="contratos.gestao.*"
                                        title="Estudo Ambiental" />

                                    <!-- Regularização Ambiental -->
                                    <NavDropdownLink route-name="contratos.gestao.listagem" param-name="tipo"
                                        route-param="3" active-on-route-prefix="contratos.gestao.*"
                                        title="Regularização Ambiental" />

                                </NavDropdown>

                                <!-- Licenças -->
                                <NavLink route-name="licenca.index" title="Licenças" :icon="IconFileCheck" />

                                <!-- Ambiente Geo -->
<!--                                <NavLink route-name="ambienteGeo" title="Ambiente Geo" :icon="IconWorld" />-->

                                <!-- Fiscal -->
                                <NavDropdown prefix="fiscal.*" title="Fiscal" :icon="IconFileCertificate">
                                    <!-- Tipo 1 -->
                                    <NavDropdownLink route-name="fiscal.index" param-name="tipo" route-param="1"
                                        active-on-route-prefix="fiscal.*" title="Gestão Ambiental" />

                                    <!-- Tipo 2 -->
                                    <NavDropdownLink route-name="fiscal.index" param-name="tipo" route-param="2"
                                        active-on-route-prefix="fiscal.*" title="Estudo Ambiental" />

                                    <!-- Tipo 3 -->
                                    <NavDropdownLink route-name="fiscal.index" param-name="tipo" route-param="3"
                                        active-on-route-prefix="fiscal.*" title="Regularização Ambiental" />
                                </NavDropdown>

                                <!-- Cadastros -->
                                <NavDropdown prefix="cadastros.*" title="Cadastros" :icon="IconClipboardList">

                                    <!-- Usuários -->
                                    <NavDropdownLink route-name="cadastros.usuarios.listagem"
                                        active-on-route-prefix="cadastros.usuarios.*" title="Usuários" />

                                    <!-- Perfis -->
                                    <NavDropdownLink route-name="cadastros.perfis.listagem"
                                        active-on-route-prefix="cadastros.perfis.*" title="Perfis" />
                                </NavDropdown>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
