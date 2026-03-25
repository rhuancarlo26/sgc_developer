<template>
    <div class="nav-item dropdown d-none d-md-flex me-3">

        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
           aria-label="Show notifications">
            <IconBell stroke-width="1" />
            <span class="badge bg-red"></span>
        </a>

        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">Notificações</h3>
                </div>

                <div class="list-group list-group-flush list-group-hoverable">
                    <div class="list-group-item" v-for="i in 3">
                        <div class="row align-items-center">
                            <div class="col-auto"><span
                                class="status-dot status-dot-animated bg-red d-block"></span></div>
                            <div class="col text-truncate">
                                <a href="#" class="text-body d-block">Notificação {{ i }}</a>
                                <div class="d-block text-secondary text-truncate mt-n1">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt ut labore et dolore magna aliqua.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>
<script setup>

import {IconBell} from "@tabler/icons-vue";

</script>
