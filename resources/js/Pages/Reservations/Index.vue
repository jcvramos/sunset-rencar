<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    reservations: Object,
    filters: Object,
    stages: Object,
});

const search = ref(props.filters?.search ?? '');
const stage  = ref(props.filters?.stage ?? '');

const apply = () => {
    router.get(route('reservations.index'), {
        search: search.value || undefined,
        stage:  stage.value || undefined,
    }, { preserveState: true, replace: true });
};

const stageBadge = (s) => ({
    cotizacion:   'bg-gray-100 text-gray-700',
    deposito:     'bg-blue-100 text-blue-700',
    validacion:   'bg-indigo-100 text-indigo-700',
    confirmacion: 'bg-cyan-100 text-cyan-700',
    entrega:      'bg-amber-100 text-amber-700',
    activa:       'bg-emerald-100 text-emerald-700',
    extension:    'bg-orange-100 text-orange-700',
    recepcion:    'bg-purple-100 text-purple-700',
    factura:      'bg-rose-100 text-rose-700',
    cerrada:      'bg-gray-200 text-gray-600',
    cancelada:    'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100');

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });
</script>

<template>
    <Head title="Reservas" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">📋 Reservas — Flujo 9 etapas</h2>
                <Link :href="route('reservations.create')"
                      class="px-4 py-2 rounded-lg text-sm font-semibold text-white"
                      style="background:#0c0a07;">
                    + Nueva reserva
                </Link>
            </div>
        </template>

        <!-- Filtros -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                <input v-model="search" @keyup.enter="apply" type="text"
                       placeholder="Folio o nombre cliente..."
                       class="w-full rounded-lg border-gray-300 text-sm" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Etapa</label>
                <select v-model="stage" @change="apply" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Todas</option>
                    <option v-for="(label, key) in stages" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>
            <button @click="apply" class="px-3 py-2 bg-gray-900 text-white text-sm rounded-lg">Filtrar</button>
        </div>

        <!-- Lista -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Folio</th>
                        <th class="px-4 py-3">Cliente</th>
                        <th class="px-4 py-3">Vehículo</th>
                        <th class="px-4 py-3">Fechas</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Etapa</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="r in reservations.data" :key="r.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ r.code }}</td>
                        <td class="px-4 py-3">
                            <span v-if="r.client">{{ r.client.first_name }} {{ r.client.last_name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="r.vehicle">{{ r.vehicle.name }} <span class="text-gray-400">({{ r.vehicle.plate }})</span></span>
                            <span v-else class="text-gray-400 italic">Sin asignar</span>
                            <span v-if="r.vehicle_type" class="ml-1">{{ r.vehicle_type.emoji }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ r.start_date }} → {{ r.end_date }}<br>
                            <span class="text-gray-400">{{ r.days }} días</span>
                        </td>
                        <td class="px-4 py-3 font-semibold">{{ fmt(r.total) }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium" :class="stageBadge(r.current_stage)">
                                {{ stages[r.current_stage] ?? r.current_stage }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="route('reservations.show', r.id)" class="text-amber-600 hover:underline text-xs">Ver →</Link>
                        </td>
                    </tr>
                    <tr v-if="!reservations.data.length">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            Sin reservas registradas. <Link :href="route('reservations.create')" class="text-amber-600">Crear la primera</Link>.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div v-if="reservations.links?.length > 3" class="mt-4 flex flex-wrap gap-1 justify-center">
            <Link v-for="link in reservations.links" :key="link.label"
                  :href="link.url ?? '#'"
                  v-html="link.label"
                  class="px-3 py-1.5 text-xs rounded border"
                  :class="link.active ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300'" />
        </div>
    </AuthenticatedLayout>
</template>
