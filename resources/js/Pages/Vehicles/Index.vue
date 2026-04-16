<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    vehicles: Array,
    vehicleTypes: Array,
});

// --- Modal ---
const showModal   = ref(false);
const editingId   = ref(null);

const form = useForm({
    plate: '', name: '', vehicle_type_id: '', brand: '', model: '',
    year: new Date().getFullYear(), color: '', seats: 5,
    status: 'disponible', is_own: true,
    daily_rate: '', deposit_amount: '', description: '',
});

const openCreate = () => {
    form.reset();
    editingId.value = null;
    showModal.value = true;
};

const openEdit = (v) => {
    form.plate          = v.plate;
    form.name           = v.name;
    form.vehicle_type_id = v.vehicle_type?.id ?? '';
    form.brand          = v.brand;
    form.model          = v.model;
    form.year           = v.year;
    form.color          = v.color;
    form.seats          = v.seats;
    form.status         = v.status;
    form.is_own         = v.is_own;
    form.daily_rate     = v.daily_rate;
    form.deposit_amount = v.deposit_amount;
    form.description    = v.description ?? '';
    editingId.value     = v.id;
    showModal.value     = true;
};

const submit = () => {
    if (editingId.value) {
        form.patch(route('vehicles.update', editingId.value), {
            onSuccess: () => { showModal.value = false; },
        });
    } else {
        form.post(route('vehicles.store'), {
            onSuccess: () => { showModal.value = false; },
        });
    }
};

const deleteVehicle = (v) => {
    if (confirm(`¿Eliminar el vehículo ${v.name} (${v.plate})?`)) {
        router.delete(route('vehicles.destroy', v.id));
    }
};

// --- Filtros ---
const filterStatus = ref('');
const filterType   = ref('');
const search       = ref('');

const filtered = computed(() => {
    return props.vehicles.filter(v => {
        const matchStatus = !filterStatus.value || v.status === filterStatus.value;
        const matchType   = !filterType.value   || v.vehicle_type?.id == filterType.value;
        const matchSearch = !search.value       ||
            v.name.toLowerCase().includes(search.value.toLowerCase()) ||
            v.plate.toLowerCase().includes(search.value.toLowerCase());
        return matchStatus && matchType && matchSearch;
    });
});

// --- Badge helpers ---
const statusBadge = {
    disponible:   'bg-green-100 text-green-800',
    rentado:      'bg-blue-100 text-blue-800',
    mantenimiento:'bg-yellow-100 text-yellow-800',
    inactivo:     'bg-gray-100 text-gray-600',
};
const statusLabel = {
    disponible:   'Disponible',
    rentado:      'Rentado',
    mantenimiento:'Mantenimiento',
    inactivo:     'Inactivo',
};
</script>

<template>
    <Head title="Flota de Vehículos" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">🚗 Flota de Vehículos</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ vehicles.length }} vehículos registrados</p>
                </div>
                <button @click="openCreate"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + Agregar vehículo
                </button>
            </div>
        </template>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
            <input v-model="search" type="text" placeholder="Buscar placa o nombre..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-red-500"/>
            <select v-model="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="rentado">Rentado</option>
                <option value="mantenimiento">Mantenimiento</option>
                <option value="inactivo">Inactivo</option>
            </select>
            <select v-model="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Todos los tipos</option>
                <option v-for="t in vehicleTypes" :key="t.id" :value="t.id">{{ t.emoji }} {{ t.name }}</option>
            </select>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Vehículo</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Tipo</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Placa</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Tarifa/día</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Depósito</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Estado</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Flota</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="v in filtered" :key="v.id" class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ v.name }}</div>
                            <div class="text-gray-400 text-xs">{{ v.brand }} {{ v.model }} {{ v.year }} · {{ v.color }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ v.vehicle_type?.emoji }} {{ v.vehicle_type?.name }}</td>
                        <td class="px-4 py-3 font-mono text-gray-800">{{ v.plate }}</td>
                        <td class="px-4 py-3 text-gray-800">L. {{ Number(v.daily_rate).toLocaleString() }}</td>
                        <td class="px-4 py-3 text-gray-800">L. {{ Number(v.deposit_amount).toLocaleString() }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusBadge[v.status]">
                                {{ statusLabel[v.status] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ v.is_own ? 'Propia' : 'Subarrendada' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-end">
                                <button @click="openEdit(v)"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50">
                                    Editar
                                </button>
                                <button @click="deleteVehicle(v)"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filtered.length === 0">
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400">
                            No se encontraron vehículos con los filtros aplicados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal crear/editar -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ editingId ? 'Editar vehículo' : 'Agregar vehículo' }}
                    </h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre descriptivo *</label>
                            <input v-model="form.name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                            <input v-model="form.plate" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-500"/>
                            <p v-if="form.errors.plate" class="text-red-500 text-xs mt-1">{{ form.errors.plate }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de vehículo *</label>
                            <select v-model="form.vehicle_type_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="">Seleccionar...</option>
                                <option v-for="t in vehicleTypes" :key="t.id" :value="t.id">{{ t.emoji }} {{ t.name }}</option>
                            </select>
                            <p v-if="form.errors.vehicle_type_id" class="text-red-500 text-xs mt-1">{{ form.errors.vehicle_type_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select v-model="form.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="disponible">Disponible</option>
                                <option value="rentado">Rentado</option>
                                <option value="mantenimiento">Mantenimiento</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca *</label>
                            <input v-model="form.brand" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                            <input v-model="form.model" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Año *</label>
                            <input v-model="form.year" type="number" min="2000" max="2030" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
                            <input v-model="form.color" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa/día (L.) *</label>
                            <input v-model="form.daily_rate" type="number" step="0.01" min="0" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                            <p v-if="form.errors.daily_rate" class="text-red-500 text-xs mt-1">{{ form.errors.daily_rate }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Depósito garantía (L.)</label>
                            <input v-model="form.deposit_amount" type="number" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div class="col-span-2 flex items-center gap-3">
                            <input v-model="form.is_own" type="checkbox" id="is_own" class="rounded border-gray-300 text-red-600"/>
                            <label for="is_own" class="text-sm text-gray-700">Vehículo de flota propia (desmarca si es subarrendado)</label>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea v-model="form.description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg disabled:opacity-50 font-medium">
                            {{ form.processing ? 'Guardando...' : (editingId ? 'Actualizar' : 'Guardar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
