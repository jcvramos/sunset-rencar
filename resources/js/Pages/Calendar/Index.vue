<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    vehicleTypes: Array,
    vehicles: Array,
});

// --- Estado del calendario ---
const today       = new Date();
const currentYear = ref(today.getFullYear());
const currentMonth = ref(today.getMonth()); // 0-based

const filterType  = ref('');
const busyMap     = ref({}); // { vehicleId: { 'YYYY-MM-DD': status } }
const loading     = ref(false);

// Nombres de meses en español
const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const dayNames   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

// Días del mes actual
const daysInMonth = computed(() => {
    const d = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
    return Array.from({ length: d }, (_, i) => i + 1);
});

const firstDayOffset = computed(() =>
    new Date(currentYear.value, currentMonth.value, 1).getDay()
);

const prevMonth = () => {
    if (currentMonth.value === 0) { currentMonth.value = 11; currentYear.value--; }
    else currentMonth.value--;
    loadAvailability();
};
const nextMonth = () => {
    if (currentMonth.value === 11) { currentMonth.value = 0; currentYear.value++; }
    else currentMonth.value++;
    loadAvailability();
};

// Vehículos filtrados por tipo
const filteredVehicles = computed(() =>
    filterType.value
        ? props.vehicles.filter(v => v.vehicle_type_id == filterType.value)
        : props.vehicles
);

// Cargar disponibilidad desde el servidor
const loadAvailability = async () => {
    loading.value = true;
    const year  = currentYear.value;
    const month = String(currentMonth.value + 1).padStart(2, '0');
    const lastDay = new Date(year, currentMonth.value + 1, 0).getDate();

    try {
        const { data } = await axios.get(route('calendar.availability'), {
            params: {
                start: `${year}-${month}-01`,
                end:   `${year}-${month}-${String(lastDay).padStart(2, '0')}`,
                vehicle_type_id: filterType.value || undefined,
            }
        });

        busyMap.value = {};
        data.forEach(v => {
            busyMap.value[v.id] = {};
            v.busy_dates.forEach(b => {
                busyMap.value[v.id][b.date] = {
                    status: b.status,
                    position: b.position,
                    reservation_id: b.reservation_id,
                };
            });
        });
    } finally {
        loading.value = false;
    }
};

const getDayInfo = (vehicleId, day) => {
    const month = String(currentMonth.value + 1).padStart(2, '0');
    const d     = String(day).padStart(2, '0');
    const key   = `${currentYear.value}-${month}-${d}`;
    return busyMap.value[vehicleId]?.[key] ?? { status: 'disponible', position: null };
};

const getDayStatus = (vehicleId, day) => getDayInfo(vehicleId, day).status;
const getDayPosition = (vehicleId, day) => getDayInfo(vehicleId, day).position;

const getDayIcon = (vehicleId, day) => {
    const pos = getDayPosition(vehicleId, day);
    if (pos === 'start')  return '🚗';
    if (pos === 'end')    return '🏁';
    if (pos === 'single') return '🚗🏁';
    return '';
};

const dayClass = (status) => ({
    'disponible':    'bg-green-100 text-green-800',
    'rentado':       'bg-blue-200 text-blue-900',
    'mantenimiento': 'bg-yellow-200 text-yellow-900',
    'reservado':     'bg-purple-200 text-purple-900',
    'bloqueado':     'bg-gray-200 text-gray-600',
}[status] ?? 'bg-gray-100');

onMounted(loadAvailability);
</script>

<template>
    <Head title="Calendario de Disponibilidad" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">📅 Calendario de Disponibilidad</h1>
                    <p class="text-sm text-gray-500 mt-1">Vista por vehículo — {{ monthNames[currentMonth] }} {{ currentYear }}</p>
                </div>
                <div class="flex gap-3 flex-wrap">
                    <select v-model="filterType" @change="loadAvailability"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Todos los tipos</option>
                        <option v-for="t in vehicleTypes" :key="t.id" :value="t.id">{{ t.emoji }} {{ t.name }}</option>
                    </select>
                    <div class="flex items-center gap-2">
                        <button @click="prevMonth" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">← Ant.</button>
                        <span class="text-sm font-medium text-gray-700 w-36 text-center">{{ monthNames[currentMonth] }} {{ currentYear }}</span>
                        <button @click="nextMonth" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">Sig. →</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Leyenda -->
        <div class="flex gap-4 flex-wrap mb-4 text-xs">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-200 inline-block"></span> Disponible</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-200 inline-block"></span> Rentado</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-200 inline-block"></span> Reservado</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-200 inline-block"></span> Mantenimiento</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-gray-200 inline-block"></span> Bloqueado</span>
            <span class="flex items-center gap-1 border-l pl-4 ml-2 border-gray-300">🚗 Inicio</span>
            <span class="flex items-center gap-1">🏁 Fin</span>
        </div>

        <!-- Calendario tipo grid -->
        <div v-if="loading" class="text-center py-16 text-gray-400">Cargando disponibilidad...</div>

        <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
            <table class="text-xs min-w-full">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold sticky left-0 bg-slate-800 min-w-40">Vehículo</th>
                        <th v-for="day in daysInMonth" :key="day"
                            class="px-1 py-3 text-center font-semibold min-w-8"
                            :class="{ 'bg-slate-600': new Date(currentYear, currentMonth, day).getDay() === 0 || new Date(currentYear, currentMonth, day).getDay() === 6 }">
                            <div>{{ dayNames[new Date(currentYear, currentMonth, day).getDay()] }}</div>
                            <div class="text-lg font-bold">{{ day }}</div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="vehicle in filteredVehicles" :key="vehicle.id" class="hover:bg-gray-50">
                        <td class="px-3 py-2 sticky left-0 bg-white border-r border-gray-200">
                            <div class="font-medium text-gray-900 truncate max-w-36">{{ vehicle.name }}</div>
                            <div class="text-gray-400 font-mono">{{ vehicle.plate }}</div>
                        </td>
                        <td v-for="day in daysInMonth" :key="day" class="px-0.5 py-1 text-center">
                            <div class="w-7 h-7 rounded flex items-center justify-center mx-auto text-[11px] font-medium cursor-default leading-none"
                                :class="dayClass(getDayStatus(vehicle.id, day))"
                                :title="getDayStatus(vehicle.id, day) + (getDayPosition(vehicle.id, day) ? ' — ' + getDayPosition(vehicle.id, day) : '')">
                                <span>{{ getDayIcon(vehicle.id, day) }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredVehicles.length === 0">
                        <td :colspan="daysInMonth.length + 1" class="px-4 py-10 text-center text-gray-400">
                            No hay vehículos para mostrar.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
