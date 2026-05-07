<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link } from '@inertiajs/vue3';

const sidebarOpen = ref(false);

const navGroups = [
    {
        title: 'General',
        items: [
            { label: 'Dashboard',  route: 'dashboard',           icon: '📊' },
        ],
    },
    {
        title: 'Operación',
        items: [
            { label: 'Reservas',    route: 'reservations.index', icon: '📋' },
            { label: 'Calendario',  route: 'calendar.index',     icon: '📅' },
            { label: 'Flota',       route: 'vehicles.index',     icon: '🚗' },
            { label: 'Clientes',    route: 'clients.index',      icon: '👥' },
        ],
    },
    {
        title: 'Finanzas',
        items: [
            { label: 'Subarrendadores', route: 'sublessors.index',          icon: '🤝' },
            { label: 'Cuentas por pagar', route: 'sublessors.payables.index', icon: '💵' },
        ],
    },
    {
        title: 'Control',
        items: [
            { label: 'Daños',         route: 'damages.index',       icon: '🛠️' },
            { label: 'Cancelaciones', route: 'cancellations.index', icon: '✖️'  },
            { label: 'Políticas',     route: 'policies.index',      icon: '⚙️'  },
        ],
    },
];
</script>

<template>
    <div class="min-h-screen flex" style="background:#f5f3ef; font-family:'Inter',sans-serif;">

        <!-- ========= SIDEBAR ========= -->
        <aside
            class="fixed lg:static inset-y-0 left-0 z-40 w-64 transition-transform duration-200 flex flex-col"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            style="background:#0c0a07; border-right:1px solid rgba(251,191,36,0.12);">

            <!-- Logo -->
            <div class="px-5 py-5 flex items-center gap-3" style="border-bottom:1px solid rgba(251,191,36,0.10);">
                <Link :href="route('dashboard')" class="shrink-0">
                    <img src="/img/logo.png" alt="Sunset RentCar" class="h-10 w-auto"
                         style="filter: drop-shadow(0 0 10px rgba(251,191,36,0.6)) brightness(1.1);"/>
                </Link>
                <div>
                    <p class="text-sm font-bold" style="color:#fbbf24;">Sunset RenCar</p>
                    <p class="text-[10px] uppercase tracking-wider" style="color:rgba(251,191,36,0.4);">Panel operativo</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5">
                <div v-for="group in navGroups" :key="group.title">
                    <p class="px-3 text-[10px] uppercase tracking-wider mb-1.5"
                       style="color:rgba(251,191,36,0.30);">
                        {{ group.title }}
                    </p>
                    <Link
                        v-for="nav in group.items"
                        :key="nav.route"
                        :href="route(nav.route)"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-all mb-0.5"
                        :style="route().current(nav.route)
                            ? 'background:rgba(251,191,36,0.15); color:#fbbf24; box-shadow: inset 3px 0 0 #fbbf24;'
                            : 'color:rgba(251,191,36,0.55);'"
                        :onmouseover="!route().current(nav.route) ? 'this.style.background=\'rgba(251,191,36,0.07)\';this.style.color=\'rgba(251,191,36,0.9)\'' : ''"
                        :onmouseout="!route().current(nav.route) ? 'this.style.background=\'transparent\';this.style.color=\'rgba(251,191,36,0.55)\'' : ''"
                    >
                        <span class="text-base leading-none">{{ nav.icon }}</span>
                        <span>{{ nav.label }}</span>
                    </Link>
                </div>
            </nav>

            <!-- Usuario al fondo -->
            <div class="px-3 py-3" style="border-top:1px solid rgba(251,191,36,0.10);">
                <Dropdown align="left" width="48">
                    <template #trigger>
                        <button class="w-full flex items-center gap-2.5 px-2 py-2 rounded-lg transition-all"
                                style="color:rgba(251,191,36,0.7);"
                                onmouseover="this.style.background='rgba(251,191,36,0.07)'"
                                onmouseout="this.style.background='transparent'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-black text-xs font-black shrink-0"
                                  style="background:linear-gradient(135deg,#fbbf24,#fb923c);">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                            <div class="flex-1 min-w-0 text-left">
                                <p class="text-xs font-medium truncate">{{ $page.props.auth.user.name }}</p>
                                <p class="text-[10px] truncate" style="color:rgba(251,191,36,0.4);">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <svg class="h-3.5 w-3.5 opacity-60" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Mi perfil</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Cerrar sesión</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </aside>

        <!-- Backdrop móvil -->
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/50 lg:hidden"></div>

        <!-- ========= CONTENT ========= -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Topbar (solo móvil) -->
            <div class="lg:hidden flex items-center justify-between px-4 py-3"
                 style="background:#0c0a07; border-bottom:1px solid rgba(251,191,36,0.10);">
                <button @click="sidebarOpen = true" class="p-1.5 rounded" style="color:rgba(251,191,36,0.7);">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <img src="/img/logo.png" alt="Sunset" class="h-8 w-auto"
                     style="filter: drop-shadow(0 0 8px rgba(251,191,36,0.5));"/>
                <div class="w-7"></div>
            </div>

            <!-- Flash messages -->
            <div v-if="$page.props.flash?.success"
                 class="px-6 py-3 text-sm flex items-center gap-2"
                 style="background:#f0fdf4; border-left:3px solid #22c55e; color:#15803d;">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error"
                 class="px-6 py-3 text-sm flex items-center gap-2"
                 style="background:#fef2f2; border-left:3px solid #ef4444; color:#b91c1c;">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                {{ $page.props.flash.error }}
            </div>

            <!-- Page header slot -->
            <header v-if="$slots.header" class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-5">
                    <slot name="header"/>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                <slot/>
            </main>
        </div>
    </div>
</template>
