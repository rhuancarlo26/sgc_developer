<script setup>

import { IconClipboardList, IconLayoutDashboard } from "@tabler/icons-vue"
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

                                <!-- Cadastros -->
                                <NavDropdown prefix="cadastros.*" title="Cadastros" :icon="IconClipboardList">

                                    <!-- Usuários -->
                                    <NavDropdownLink route-name="cadastros.usuarios.listagem"
                                        active-on-route-prefix="cadastros.usuarios.*" title="Usuários" />

                                    <!-- Perfis -->
                                    <NavDropdownLink route-name="cadastros.perfis.listagem"
                                        active-on-route-prefix="cadastros.perfis.*" title="Perfis" />

                                </NavDropdown>

                                <!-- Contratos -->
                                <NavDropdown prefix="contratos.*" title="Contratos" :icon="IconLayoutDashboard">

                                    <!-- Gestão Ambiental -->
                                    <NavDropdownLink route-name="contratos.gestao.listagem" route-param="1"
                                        active-on-route-prefix="contratos.gestao.*" title="Gestão Ambiental" />

                                    <!-- Estudo Ambiental -->
                                    <NavDropdownLink route-name="contratos.gestao.listagem" route-param="2"
                                        active-on-route-prefix="contratos.gestao.*" title="Estudo Ambiental" />

                                    <!-- Regularização Ambiental -->
                                    <NavDropdownLink route-name="contratos.gestao.listagem" route-param="3"
                                        active-on-route-prefix="contratos.gestao.*" title="Regularização Ambiental" />

                                </NavDropdown>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
