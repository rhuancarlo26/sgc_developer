<script setup>
import {Link} from '@inertiajs/vue3'
</script>

<template>
    <div class="nav-item dropdown">

        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
           aria-label="Open user menu">
            <span class="avatar avatar-sm">
                  {{ $page.props.auth.user.name.substring(0, 2) }}
            </span>
            <div class="d-none d-xl-block ps-2">
                <div>{{ $page.props.auth.user.name }}</div>
                 <div class="mt-1 small text-secondary">{{$page.props.auth.user.email}}</div>
            </div>
        </a>

        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
            <Link :href="route('profile.edit')" class="dropdown-item">Perfil</Link>
            <Link :href="route('logout')" method="post" as="button" class="dropdown-item">Sair</Link>
        </div>
    </div>
</template>
