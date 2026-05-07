<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    damages: Object,
    vehicles: Array,
    filters: Object,
});

const showModal = ref(false);
const status    = ref(props.filters?.status ?? '');
const vehicleId = ref(props.filters?.vehicle_id ?? '');

const form = useForm({
    vehicle_id: '', client_id: '', reservation_id: '',
    zone: 'frontal', description: '',
    evidence_photo: null, amount_charged: 0,
    linked_to_deposit: false, occurred_at: '', notes: '',
});

const open = () => { form.reset(); showModal.value = true; };

const submit = () => form.post(route('damages.store'), {
    forceFormData: true,
    onSuccess: () => showModal.value = false,
});

const updateStatus = (d, newStatus) => {
    router.patch(route('damages.update', d.id), { status: newStatus, amount_charged: d.amount_charged ?? 0 }, { preserveScroll: true });
};

const apply = () => router.get(route('damages.index'), {
    status: status.value || undefined,
    vehicle_id: vehicleId.value || undefined,
}, { preserveState: true, replace: true });

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });

const statusBadge = (s) => ({
    pendiente: 'bg-amber-100 text-amber-800',
    cobrado:   'bg-emerald-100 text-emerald-800',
    disputado: 'bg-blue-100 text-blue-800',
    absorbido: 'bg-gray-200 text-gray-700',
}[s] ?? 'bg-gray-100');
</script>

<template>
    <Head title="Daños" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">🛠 Control de daños</h2>
                <button @click="open" class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background:#0c0a07;">
                    + Registrar daño
                </button>
            </div>
        </template>

        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap gap-3">
            <select v-model="status" @change="apply" class="rounded-lg border-gray-300 text-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="cobrado">Cobrado</option>
                <option value="disputado">Disputado</option>
                <option value="absorbido">Absorbido</option>
            </select>
            <select v-model="vehicleId" @change="apply" class="rounded-lg border-gray-300 text-sm">
                <option value="">Todos los vehículos</option>
                <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.name }} ({{ v.plate }})</option>
            </select>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Vehículo</th>
                        <th class="px-4 py-3 text-left">Reserva</th>
                        <th class="px-4 py-3 text-left">Zona</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="d in damages.data" :key="d.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ d.vehicle?.name }} <span class="text-xs text-gray-400">({{ d.vehicle?.plate }})</span></td>
                        <td class="px-4 py-3 font-mono text-xs">{{ d.reservation?.code ?? '—' }}</td>
                        <td class="px-4 py-3">{{ d.zone }}</td>
                        <td class="px-4 py-3 text-xs">{{ d.description }}</td>
                        <td class="px-4 py-3">
                            <span v-if="d.client">{{ d.client.first_name }} {{ d.client.last_name }}</span>
                            <span v-else class="text-gray-400">—</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">{{ fmt(d.amount_charged) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs" :class="statusBadge(d.status)">{{ d.status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-xs space-x-1">
                            <button v-if="d.status !== 'cobrado'" @click="updateStatus(d, 'cobrado')" class="text-emerald-600 hover:underline">Cobrar</button>
                            <button v-if="d.status !== 'absorbido'" @click="updateStatus(d, 'absorbido')" class="text-gray-500 hover:underline">Absorber</button>
                        </td>
                    </tr>
                    <tr v-if="!damages.data.length">
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">Sin daños registrados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-xl w-full p-6">
                <h3 class="font-semibold text-lg mb-4">Registrar daño</h3>
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div>
                        <label class="text-xs text-gray-500">Vehículo *</label>
                        <select v-model="form.vehicle_id" required class="w-full rounded border-gray-300">
                            <option value="">—</option>
                            <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.name }} ({{ v.plate }})</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Zona afectada *</label>
                        <select v-model="form.zone" required class="w-full rounded border-gray-300">
                            <option value="frontal">Frontal</option>
                            <option value="trasera">Trasera</option>
                            <option value="lateral_izq">Lateral Izquierdo</option>
                            <option value="lateral_der">Lateral Derecho</option>
                            <option value="interior_frontal">Interior Frontal</option>
                            <option value="interior_trasero">Interior Trasero</option>
                            <option value="tablero">Tablero</option>
                            <option value="odometro">Odómetro</option>
                            <option value="mecanico">Mecánico</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs text-gray-500">Descripción *</label>
                        <textarea v-model="form.description" rows="3" required class="w-full rounded border-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Foto evidencia</label>
                        <input type="file" accept="image/*" @change="form.evidence_photo = $event.target.files[0]"
                               class="block w-full text-xs" />
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Monto a cobrar (L.)</label>
                        <input v-model="form.amount_charged" type="number" step="0.01" min="0" class="w-full rounded border-gray-300" />
                    </div>
                    <label class="md:col-span-2 inline-flex items-center gap-2 text-xs">
                        <input type="checkbox" v-model="form.linked_to_deposit" class="rounded" />
                        Cobrar contra depósito en garantía
                    </label>
                    <div class="md:col-span-2 flex justify-end gap-2 pt-2 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded text-sm">Cancelar</button>
                        <button type="submit" :disabled="form.processing"
                                class="px-4 py-2 text-white rounded text-sm font-semibold" style="background:#0c0a07;">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
