<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
});

const statCards = [
    { label: 'Total Flota',       value: props.stats.total_vehicles,       icon: '🚗', color: 'bg-slate-700',  href: 'vehicles.index' },
    { label: 'Disponibles',       value: props.stats.available_vehicles,   icon: '✅', color: 'bg-green-600',  href: 'vehicles.index' },
    { label: 'Rentados',          value: props.stats.rented_vehicles,      icon: '🔑', color: 'bg-blue-600',   href: 'vehicles.index' },
    { label: 'Mantenimiento',     value: props.stats.maintenance_vehicles, icon: '🔧', color: 'bg-yellow-500', href: 'vehicles.index' },
    { label: 'Total Clientes',    value: props.stats.total_clients,        icon: '👥', color: 'bg-slate-700',  href: 'clients.index'  },
    { label: 'Clientes VIP',      value: props.stats.vip_clients,          icon: '⭐', color: 'bg-purple-600', href: 'clients.index'  },
    { label: 'Clientes Bloqueados', value: props.stats.blocked_clients,    icon: '🚫', color: 'bg-red-600',    href: 'clients.index'  },
];
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Operativo</h1>
                    <p class="text-sm text-gray-500 mt-1">Resumen general de Sunset RenCar</p>
                </div>
                <Link :href="route('calendar.index')"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    📅 Ver Calendario
                </Link>
            </div>
        </template>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
            <Link v-for="card in statCards" :key="card.label"
                :href="route(card.href)"
                class="rounded-xl text-white p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-2"
                :class="card.color">
                <span class="text-3xl">{{ card.icon }}</span>
                <span class="text-4xl font-bold">{{ card.value }}</span>
                <span class="text-sm opacity-90">{{ card.label }}</span>
            </Link>
        </div>

        <!-- Accesos rápidos -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Accesos rápidos</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <Link :href="route('vehicles.index')"
                    class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-colors text-center">
                    <span class="text-2xl">🚗</span>
                    <span class="text-sm font-medium text-gray-700">Gestionar Flota</span>
                </Link>
                <Link :href="route('clients.index')"
                    class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-colors text-center">
                    <span class="text-2xl">👥</span>
                    <span class="text-sm font-medium text-gray-700">Ver Clientes</span>
                </Link>
                <Link :href="route('calendar.index')"
                    class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-colors text-center">
                    <span class="text-2xl">📅</span>
                    <span class="text-sm font-medium text-gray-700">Disponibilidad</span>
                </Link>
                <Link :href="route('policies.index')"
                    class="flex flex-col items-center gap-2 p-4 rounded-lg border border-gray-200 hover:border-red-300 hover:bg-red-50 transition-colors text-center">
                    <span class="text-2xl">⚙️</span>
                    <span class="text-sm font-medium text-gray-700">Políticas</span>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
