<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    clients: Array,
    vehicleTypes: Array,
    vehicles: Array,
    destinations: Array,
});

const form = useForm({
    client_id: '',
    vehicle_type_id: '',
    vehicle_id: '',
    start_date: '',
    end_date: '',
    daily_rate: 0,
    discount: 0,
    deposit_amount: 0,
    destination_municipality: '',
    destination: '',
    notes: '',
    source: 'manual',
});

const filteredVehicles = computed(() =>
    !form.vehicle_type_id ? props.vehicles : props.vehicles.filter(v => v.vehicle_type_id === Number(form.vehicle_type_id))
);

watch(() => form.vehicle_id, (id) => {
    const v = props.vehicles.find(x => x.id === Number(id));
    if (v) {
        form.daily_rate     = v.daily_rate;
        form.deposit_amount = v.deposit_amount;
    }
});

const days = computed(() => {
    if (!form.start_date || !form.end_date) return 0;
    const s = new Date(form.start_date), e = new Date(form.end_date);
    return Math.max(1, Math.round((e - s) / 86400000) + 1);
});

const subtotal = computed(() => Number(form.daily_rate) * days.value);
const total    = computed(() => Math.max(0, subtotal.value - Number(form.discount || 0)));

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });

const submit = () => form.post(route('reservations.store'));
</script>

<template>
    <Head title="Nueva reserva" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900">📋 Nueva reserva</h2>
        </template>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-gray-200 p-6 space-y-5 max-w-4xl">
            <div v-if="Object.keys(form.errors).length"
                 class="bg-red-50 border border-red-300 rounded-lg p-3 text-sm text-red-700">
                <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
                <ul class="list-disc ml-5 text-xs">
                    <li v-for="(msg, key) in form.errors" :key="key">
                        <strong>{{ key }}:</strong> {{ msg }}
                    </li>
                </ul>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cliente *</label>
                    <select v-model="form.client_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Selecciona...</option>
                        <option v-for="c in clients" :key="c.id" :value="c.id" :disabled="c.status === 'bloqueado'">
                            {{ c.first_name }} {{ c.last_name }}
                            <span v-if="c.status === 'bloqueado'">(BLOQUEADO)</span>
                        </option>
                    </select>
                    <p v-if="form.errors.client_id" class="text-xs text-red-600 mt-1">{{ form.errors.client_id }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de vehículo *</label>
                    <select v-model="form.vehicle_type_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Selecciona...</option>
                        <option v-for="t in vehicleTypes" :key="t.id" :value="t.id">{{ t.emoji }} {{ t.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehículo asignado</label>
                    <select v-model="form.vehicle_id" class="w-full rounded-lg border-gray-300">
                        <option value="">— Por asignar después —</option>
                        <option v-for="v in filteredVehicles" :key="v.id" :value="v.id"
                                :disabled="v.status === 'inactivo'">
                            {{ v.name }} ({{ v.plate }}) — {{ v.status }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Destino (municipio)</label>
                    <select v-model="form.destination_municipality" class="w-full rounded-lg border-gray-300">
                        <option value="">Selecciona...</option>
                        <option v-for="d in destinations" :key="d.id" :value="d.municipality"
                                :disabled="d.status === 'bloqueado'">
                            {{ d.municipality }} — {{ d.status }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio *</label>
                    <input v-model="form.start_date" type="date" required class="w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin *</label>
                    <input v-model="form.end_date" type="date" required class="w-full rounded-lg border-gray-300" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tarifa diaria (L.) *</label>
                    <input v-model="form.daily_rate" type="number" step="0.01" min="0" required class="w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descuento (L.)</label>
                    <input v-model="form.discount" type="number" step="0.01" min="0" class="w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Depósito en garantía (L.)</label>
                    <input v-model="form.deposit_amount" type="number" step="0.01" min="0" class="w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Origen</label>
                    <select v-model="form.source" class="w-full rounded-lg border-gray-300">
                        <option value="manual">Manual</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="bot">Bot IA</option>
                        <option value="web">Web</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Detalle de destino</label>
                <input v-model="form.destination" type="text" placeholder="Ej: Hotel Copantl, San Pedro Sula"
                       class="w-full rounded-lg border-gray-300" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notas internas</label>
                <textarea v-model="form.notes" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
            </div>

            <!-- Resumen -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500">Días</p>
                    <p class="font-semibold">{{ days }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Subtotal</p>
                    <p class="font-semibold">{{ fmt(subtotal) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Descuento</p>
                    <p class="font-semibold text-red-600">- {{ fmt(form.discount) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="font-bold text-emerald-700">{{ fmt(total) }}</p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="submit" :disabled="form.processing"
                        class="px-5 py-2 rounded-lg text-white font-semibold"
                        style="background:#0c0a07;">
                    Crear cotización
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
