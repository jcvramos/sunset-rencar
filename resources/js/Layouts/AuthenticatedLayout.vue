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
    <div class="min-h-screen" style="background:#f5f3ef; font-family:'Inter',sans-serif;">

        <!-- Navbar -->
        <nav style="background:#0c0a07; border-bottom:1px solid rgba(251,191,36,0.12);">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Logo + Nav links -->
                    <div class="flex items-center gap-5">
                        <Link :href="route('dashboard')" class="shrink-0">
                            <img src="/img/logo.png" alt="Sunset RentCar" class="h-10 w-auto"
                                 style="filter: drop-shadow(0 0 10px rgba(251,191,36,0.6)) brightness(1.1);"/>
                        </Link>

                        <!-- Separador -->
                        <div class="hidden sm:block w-px h-6" style="background:rgba(251,191,36,0.15);"></div>

                        <div class="hidden sm:flex gap-0.5">
                            <Link
                                v-for="nav in navLinks"
                                :key="nav.route"
                                :href="route(nav.route)"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition-all"
                                :style="route().current(nav.route)
                                    ? 'background:rgba(251,191,36,0.15); color:#fbbf24;'
                                    : 'color:rgba(251,191,36,0.45);'"
                                :onmouseover="!route().current(nav.route) ? 'this.style.background=\'rgba(251,191,36,0.07)\';this.style.color=\'rgba(251,191,36,0.8)\'' : ''"
                                :onmouseout="!route().current(nav.route) ? 'this.style.background=\'transparent\';this.style.color=\'rgba(251,191,36,0.45)\'' : ''"
                            >
                                <span class="text-base leading-none">{{ nav.icon }}</span>
                                <span>{{ nav.label }}</span>
                            </Link>
                        </div>
                    </div>

                    <!-- User menu -->
                    <div class="hidden sm:flex items-center">
                        <Dropdown align="right" width="44">
                            <template #trigger>
                                <button class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all"
                                        style="color:rgba(251,191,36,0.6);"
                                        onmouseover="this.style.background='rgba(251,191,36,0.07)';this.style.color='rgba(251,191,36,0.9)'"
                                        onmouseout="this.style.background='transparent';this.style.color='rgba(251,191,36,0.6)'">
                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-black text-xs font-black"
                                          style="background:linear-gradient(135deg,#fbbf24,#fb923c);">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </span>
                                    <span class="text-sm font-medium">{{ $page.props.auth.user.name }}</span>
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

                    <!-- Hamburger móvil -->
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="sm:hidden p-2 rounded-lg transition-colors"
                        style="color:rgba(251,191,36,0.5);"
                        onmouseover="this.style.background='rgba(251,191,36,0.07)'"
                        onmouseout="this.style.background='transparent'">
                        <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path v-if="!showingNavigationDropdown" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-show="showingNavigationDropdown"
                 class="sm:hidden px-4 py-3 space-y-1"
                 style="border-top:1px solid rgba(251,191,36,0.10);">
                <Link v-for="nav in navLinks" :key="nav.route" :href="route(nav.route)"
                    class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-all"
                    :style="route().current(nav.route)
                        ? 'background:rgba(251,191,36,0.15); color:#fbbf24;'
                        : 'color:rgba(251,191,36,0.5);'">
                    {{ nav.icon }} {{ nav.label }}
                </Link>
                <div class="pt-2 mt-1" style="border-top:1px solid rgba(251,191,36,0.10);">
                    <p class="px-3 text-xs mb-1" style="color:rgba(251,191,36,0.25);">{{ $page.props.auth.user.email }}</p>
                    <Link :href="route('logout')" method="post" as="button"
                        class="flex items-center gap-2 w-full text-left px-3 py-2.5 rounded-lg text-sm transition-all"
                        style="color:rgba(251,191,36,0.5);">
                        Cerrar sesión
                    </Link>
                </div>
            </div>
        </nav>

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
