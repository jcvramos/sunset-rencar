<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    destinations:  Array,
    cancellations: Array,
});

// --- Destinos ---
const showDestModal = ref(false);
const editingDestId = ref(null);

const destForm = useForm({
    municipality: '', department: '', status: 'autorizado', notes: '',
});

const openDestCreate = () => {
    destForm.reset();
    editingDestId.value = null;
    showDestModal.value = true;
};
const openDestEdit = (d) => {
    destForm.municipality = d.municipality;
    destForm.department   = d.department ?? '';
    destForm.status       = d.status;
    destForm.notes        = d.notes ?? '';
    editingDestId.value   = d.id;
    showDestModal.value   = true;
};
const submitDest = () => {
    if (editingDestId.value) {
        destForm.patch(route('policies.destinations.update', editingDestId.value), {
            onSuccess: () => { showDestModal.value = false; },
        });
    } else {
        destForm.post(route('policies.destinations.store'), {
            onSuccess: () => { showDestModal.value = false; },
        });
    }
};
const deleteDest = (d) => {
    if (confirm(`¿Eliminar destino "${d.municipality}"?`)) {
        router.delete(route('policies.destinations.destroy', d.id));
    }
};

// --- Cancelaciones ---
const showCancelModal = ref(false);
const editingCancelId = ref(null);

const cancelForm = useForm({
    hours_before: 0, hours_before_max: '', refund_percentage: 100, label: '',
});

const openCancelCreate = () => {
    cancelForm.reset();
    editingCancelId.value = null;
    showCancelModal.value = true;
};
const openCancelEdit = (c) => {
    cancelForm.hours_before     = c.hours_before;
    cancelForm.hours_before_max = c.hours_before_max ?? '';
    cancelForm.refund_percentage = c.refund_percentage;
    cancelForm.label            = c.label;
    editingCancelId.value       = c.id;
    showCancelModal.value       = true;
};
const submitCancel = () => {
    if (editingCancelId.value) {
        cancelForm.patch(route('policies.cancellations.update', editingCancelId.value), {
            onSuccess: () => { showCancelModal.value = false; },
        });
    } else {
        cancelForm.post(route('policies.cancellations.store'), {
            onSuccess: () => { showCancelModal.value = false; },
        });
    }
};
const deleteCancel = (c) => {
    if (confirm(`¿Eliminar política "${c.label}"?`)) {
        router.delete(route('policies.cancellations.destroy', c.id));
    }
};

// --- Badge helpers ---
const destBadge = {
    autorizado:  'bg-green-100 text-green-800',
    restringido: 'bg-yellow-100 text-yellow-800',
    bloqueado:   'bg-red-100 text-red-800',
};
</script>

<template>
    <Head title="Políticas del Sistema" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-2xl font-bold text-gray-900">⚙️ Políticas del Sistema</h1>
            <p class="text-sm text-gray-500 mt-1">Configura destinos autorizados y penalidades por cancelación</p>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Destinos autorizados -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">📍 Destinos</h2>
                    <button @click="openDestCreate"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        + Agregar destino
                    </button>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Municipio</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Estado</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="d in destinations" :key="d.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ d.municipality }}</div>
                                    <div class="text-xs text-gray-400">{{ d.department ?? '' }}</div>
                                    <div v-if="d.notes" class="text-xs text-gray-500 italic">{{ d.notes }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium capitalize" :class="destBadge[d.status]">
                                        {{ d.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-end">
                                        <button @click="openDestEdit(d)"
                                            class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">Editar</button>
                                        <button @click="deleteDest(d)"
                                            class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Políticas de cancelación -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">❌ Cancelaciones</h2>
                    <button @click="openCancelCreate"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        + Nueva política
                    </button>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Condición</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Devolución</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="c in cancellations" :key="c.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ c.label }}</div>
                                    <div class="text-xs text-gray-400">
                                        Desde {{ c.hours_before }}h
                                        <template v-if="c.hours_before_max"> hasta {{ c.hours_before_max }}h</template>
                                        <template v-else> en adelante</template>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-lg font-bold"
                                        :class="c.refund_percentage >= 100 ? 'text-green-600' : c.refund_percentage > 0 ? 'text-yellow-600' : 'text-red-600'">
                                        {{ c.refund_percentage }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 justify-end">
                                        <button @click="openCancelEdit(c)"
                                            class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 rounded hover:bg-blue-50">Editar</button>
                                        <button @click="deleteCancel(c)"
                                            class="text-red-500 hover:text-red-700 text-xs px-2 py-1 rounded hover:bg-red-50">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Destino -->
        <div v-if="showDestModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">{{ editingDestId ? 'Editar destino' : 'Nuevo destino' }}</h2>
                    <button @click="showDestModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <form @submit.prevent="submitDest" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Municipio *</label>
                        <input v-model="destForm.municipality" type="text" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        <p v-if="destForm.errors.municipality" class="text-red-500 text-xs mt-1">{{ destForm.errors.municipality }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input v-model="destForm.department" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select v-model="destForm.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="autorizado">Autorizado</option>
                            <option value="restringido">Restringido (requiere aprobación)</option>
                            <option value="bloqueado">Bloqueado (no permitido)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <input v-model="destForm.notes" type="text"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showDestModal = false"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button type="submit" :disabled="destForm.processing"
                            class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg disabled:opacity-50 font-medium">
                            {{ destForm.processing ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Cancelación -->
        <div v-if="showCancelModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">{{ editingCancelId ? 'Editar política' : 'Nueva política' }}</h2>
                    <button @click="showCancelModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <form @submit.prevent="submitCancel" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                        <input v-model="cancelForm.label" type="text" required
                            placeholder="+48h de anticipación = 100% devolución"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horas mínimas *</label>
                            <input v-model="cancelForm.hours_before" type="number" min="0" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horas máximas</label>
                            <input v-model="cancelForm.hours_before_max" type="number" min="0"
                                placeholder="Dejar vacío = sin límite"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">% de devolución *</label>
                            <input v-model="cancelForm.refund_percentage" type="number" min="0" max="100" step="0.01" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showCancelModal = false"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                        <button type="submit" :disabled="cancelForm.processing"
                            class="px-4 py-2 text-sm text-white bg-red-600 hover:bg-red-700 rounded-lg disabled:opacity-50 font-medium">
                            {{ cancelForm.processing ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
