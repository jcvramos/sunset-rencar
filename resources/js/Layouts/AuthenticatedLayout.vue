<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();

const navLinks = [
    { label: 'Dashboard',   route: 'dashboard',       icon: '📊' },
    { label: 'Flota',       route: 'vehicles.index',  icon: '🚗' },
    { label: 'Clientes',    route: 'clients.index',   icon: '👥' },
    { label: 'Calendario',  route: 'calendar.index',  icon: '📅' },
    { label: 'Políticas',   route: 'policies.index',  icon: '⚙️'  },
];
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Navbar -->
        <nav class="bg-slate-900 border-b border-slate-700">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Logo + Nav links -->
                    <div class="flex items-center gap-6">
                        <Link :href="route('dashboard')" class="text-white font-bold text-lg tracking-wide shrink-0">
                            🌅 Sunset RenCar
                        </Link>
                        <div class="hidden sm:flex gap-1">
                            <Link
                                v-for="nav in navLinks"
                                :key="nav.route"
                                :href="route(nav.route)"
                                class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                :class="route().current(nav.route)
                                    ? 'bg-red-600 text-white'
                                    : 'text-slate-300 hover:bg-slate-700 hover:text-white'"
                            >
                                {{ nav.icon }} {{ nav.label }}
                            </Link>
                        </div>
                    </div>

                    <!-- User menu -->
                    <div class="hidden sm:flex items-center">
                        <Dropdown align="right" width="44">
                            <template #trigger>
                                <button class="flex items-center gap-2 text-sm text-slate-300 hover:text-white transition-colors px-3 py-2 rounded-md hover:bg-slate-700">
                                    <span class="w-7 h-7 bg-red-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </span>
                                    {{ $page.props.auth.user.name }}
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
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

                    <!-- Hamburger móvil -->
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="sm:hidden p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-700">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-show="showingNavigationDropdown" class="sm:hidden border-t border-slate-700 px-4 py-2 space-y-1">
                <Link v-for="nav in navLinks" :key="nav.route" :href="route(nav.route)"
                    class="block px-3 py-2 rounded-md text-sm font-medium"
                    :class="route().current(nav.route) ? 'bg-red-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'">
                    {{ nav.icon }} {{ nav.label }}
                </Link>
                <div class="border-t border-slate-700 pt-2 mt-2">
                    <p class="px-3 text-xs text-slate-500">{{ $page.props.auth.user.email }}</p>
                    <Link :href="route('logout')" method="post" as="button"
                        class="block w-full text-left px-3 py-2 text-sm text-slate-300 hover:bg-slate-700 rounded-md">
                        Cerrar sesión
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Flash messages -->
        <div v-if="$page.props.flash?.success" class="bg-green-50 border-l-4 border-green-500 px-6 py-3 text-green-800 text-sm">
            ✓ {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="bg-red-50 border-l-4 border-red-500 px-6 py-3 text-red-800 text-sm">
            ✕ {{ $page.props.flash.error }}
        </div>

        <!-- Page header slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-5">
                <slot name="header"/>
            </div>
        </header>

        <!-- Content -->
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <slot/>
        </main>
    </div>
</template>
