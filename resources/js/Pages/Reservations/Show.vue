<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    reservation: Object,
    stages: Object,
    requiredZones: Array,
});

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });

const FLOW = ['cotizacion', 'deposito', 'validacion', 'confirmacion', 'entrega', 'activa', 'extension', 'recepcion', 'factura'];

const currentIndex = computed(() => FLOW.indexOf(props.reservation.current_stage));
const nextStage    = computed(() => FLOW[currentIndex.value + 1] ?? null);

const ZONE_LABELS = {
    frontal:           'Frontal',
    trasera:           'Trasera',
    lateral_izq:       'Lateral Izq.',
    lateral_der:       'Lateral Der.',
    interior_frontal:  'Interior Frontal',
    interior_trasero:  'Interior Trasero',
    tablero:           'Tablero',
    odometro:          'Odómetro',
};

const photoFor = (type, zone) => props.reservation.photos?.find(p => p.type === type && p.zone === zone);
const deliveryProgress = computed(() => props.requiredZones.filter(z => photoFor('entrega', z)).length);
const receptionProgress = computed(() => props.requiredZones.filter(z => photoFor('recepcion', z)).length);

// === Forms ===
const depositForm = useForm({ deposit_amount: props.reservation.deposit_amount, reference: '' });
const advanceForm = useForm({ to_stage: '', notes: '' });
const cancelForm  = useForm({ reason: '' });
const photoForm   = useForm({ type: 'entrega', zone: '', photo: null, notes: '' });
const extForm     = useForm({ new_end_date: '', additional_days: 1, additional_amount: 0, reason: '' });

const submitDeposit = () => depositForm.post(route('reservations.deposit', props.reservation.id), { preserveScroll: true });

const advance = (toStage) => {
    advanceForm.to_stage = toStage;
    advanceForm.post(route('reservations.advance', props.reservation.id), { preserveScroll: true });
};

const submitCancel = () => {
    if (!confirm('¿Confirmar cancelación de la reserva? Aplicará la política según anticipación.')) return;
    cancelForm.post(route('reservations.cancellation.store', props.reservation.id), { preserveScroll: true });
};

const uploadPhoto = (type, zone, event) => {
    photoForm.type  = type;
    photoForm.zone  = zone;
    photoForm.photo = event.target.files[0];
    photoForm.post(route('reservations.photos.store', props.reservation.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => { event.target.value = ''; photoForm.reset('photo'); },
    });
};

const submitExtension = () => extForm.post(route('reservations.extensions.store', props.reservation.id), {
    preserveScroll: true,
    forceFormData: true,
});

const stageBadge = (s) => ({
    cotizacion: 'bg-gray-200', deposito: 'bg-blue-200', validacion: 'bg-indigo-200',
    confirmacion: 'bg-cyan-200', entrega: 'bg-amber-200', activa: 'bg-emerald-200',
    extension: 'bg-orange-200', recepcion: 'bg-purple-200', factura: 'bg-rose-200',
    cerrada: 'bg-gray-300', cancelada: 'bg-red-200',
}[s] ?? 'bg-gray-200');
</script>

<template>
    <Head :title="reservation.code" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">📋 {{ reservation.code }}</h2>
                <Link :href="route('reservations.index')" class="text-sm text-gray-500">← Volver</Link>
            </div>
        </template>

        <!-- Progreso de etapas -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <div class="flex flex-wrap gap-2">
                <div v-for="(label, key, idx) in stages" :key="key"
                     v-show="!['cerrada','cancelada'].includes(key) || reservation.current_stage === key"
                     class="px-2 py-1 rounded text-xs"
                     :class="reservation.current_stage === key ? 'bg-amber-400 text-black font-bold' : (FLOW.indexOf(key) < currentIndex ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-500')">
                    {{ label }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Resumen general -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Resumen</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500">Cliente</p>
                        <p class="font-semibold">{{ reservation.client?.first_name }} {{ reservation.client?.last_name }}</p>
                        <p class="text-xs text-gray-500">{{ reservation.client?.phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Vehículo</p>
                        <p class="font-semibold">{{ reservation.vehicle?.name ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ reservation.vehicle?.plate }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Subarrendador</p>
                        <p class="font-semibold">{{ reservation.sublessor?.name ?? 'Flota propia' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Fechas</p>
                        <p class="font-semibold">{{ reservation.start_date }} → {{ reservation.end_date }}</p>
                        <p class="text-xs text-gray-500">{{ reservation.days }} días</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Destino</p>
                        <p class="font-semibold">{{ reservation.destination_municipality ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ reservation.destination }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="font-bold text-emerald-700">{{ fmt(reservation.total) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Depósito</p>
                        <p class="font-semibold">{{ fmt(reservation.deposit_amount) }}
                            <span class="text-xs ml-1"
                                  :class="reservation.deposit_status === 'pagado' ? 'text-emerald-600' : 'text-amber-600'">
                                ({{ reservation.deposit_status }})
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Origen</p>
                        <p class="font-semibold capitalize">{{ reservation.source }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Etapa</p>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium" :class="stageBadge(reservation.current_stage)">
                            {{ stages[reservation.current_stage] }}
                        </span>
                    </div>
                </div>

                <div v-if="reservation.notes" class="mt-4 text-xs text-gray-500 border-t pt-3">
                    <strong>Notas:</strong> {{ reservation.notes }}
                </div>
            </div>

            <!-- Acciones por etapa -->
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Acciones</h3>

                <!-- Etapa 2: Depósito -->
                <div v-if="reservation.current_stage === 'deposito' || (reservation.current_stage === 'cotizacion' && reservation.deposit_amount > 0)"
                     class="mb-4 pb-4 border-b">
                    <p class="text-xs font-medium text-gray-700 mb-2">Registrar depósito</p>
                    <input v-model="depositForm.deposit_amount" type="number" step="0.01" min="0"
                           class="w-full rounded border-gray-300 text-sm mb-2" placeholder="Monto" />
                    <input v-model="depositForm.reference" type="text"
                           class="w-full rounded border-gray-300 text-sm mb-2" placeholder="Referencia bancaria" />
                    <button @click="submitDeposit" class="w-full bg-blue-600 text-white text-sm py-1.5 rounded">
                        Registrar pago
                    </button>
                </div>

                <!-- Avanzar etapa -->
                <div v-if="nextStage && !['cerrada','cancelada'].includes(reservation.current_stage)" class="mb-4 pb-4 border-b">
                    <p class="text-xs font-medium text-gray-700 mb-2">Siguiente etapa</p>
                    <button @click="advance(nextStage)"
                            class="w-full bg-amber-500 text-black text-sm py-1.5 rounded font-semibold">
                        Avanzar → {{ stages[nextStage] }}
                    </button>
                </div>

                <!-- Cancelar -->
                <div v-if="!['cerrada','cancelada'].includes(reservation.current_stage)" class="mb-4">
                    <p class="text-xs font-medium text-gray-700 mb-2">Cancelar reserva</p>
                    <textarea v-model="cancelForm.reason" rows="2"
                              class="w-full rounded border-gray-300 text-xs mb-2" placeholder="Motivo..."></textarea>
                    <button @click="submitCancel" class="w-full bg-red-600 text-white text-sm py-1.5 rounded">
                        Cancelar reserva
                    </button>
                </div>
            </div>

            <!-- Fotos -->
            <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">📸 Módulo Fotográfico Guiado (8 zonas)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- ENTREGA -->
                    <div>
                        <p class="text-sm font-semibold mb-2">Entrega
                            <span class="text-xs font-normal text-gray-500">({{ deliveryProgress }}/8)</span>
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div v-for="zone in requiredZones" :key="'e-'+zone"
                                 class="border rounded-lg p-2 text-center"
                                 :class="photoFor('entrega', zone) ? 'border-emerald-400 bg-emerald-50' : 'border-dashed border-gray-300'">
                                <p class="text-xs font-medium">{{ ZONE_LABELS[zone] }}</p>
                                <img v-if="photoFor('entrega', zone)"
                                     :src="`/storage/${photoFor('entrega', zone).path}`"
                                     class="mt-1 w-full h-20 object-cover rounded" />
                                <label v-else class="block mt-1 cursor-pointer text-xs text-amber-600 hover:underline">
                                    + Subir
                                    <input type="file" accept="image/*" capture="environment" class="hidden"
                                           @change="uploadPhoto('entrega', zone, $event)" />
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- RECEPCION -->
                    <div>
                        <p class="text-sm font-semibold mb-2">Recepción
                            <span class="text-xs font-normal text-gray-500">({{ receptionProgress }}/8)</span>
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <div v-for="zone in requiredZones" :key="'r-'+zone"
                                 class="border rounded-lg p-2 text-center"
                                 :class="photoFor('recepcion', zone) ? 'border-emerald-400 bg-emerald-50' : 'border-dashed border-gray-300'">
                                <p class="text-xs font-medium">{{ ZONE_LABELS[zone] }}</p>
                                <img v-if="photoFor('recepcion', zone)"
                                     :src="`/storage/${photoFor('recepcion', zone).path}`"
                                     class="mt-1 w-full h-20 object-cover rounded" />
                                <label v-else class="block mt-1 cursor-pointer text-xs text-amber-600 hover:underline">
                                    + Subir
                                    <input type="file" accept="image/*" capture="environment" class="hidden"
                                           @change="uploadPhoto('recepcion', zone, $event)" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extensión -->
            <div v-if="['activa','entrega'].includes(reservation.current_stage)"
                 class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">⏱ Extensión de días</h3>
                <form @submit.prevent="submitExtension" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input v-model="extForm.new_end_date" type="date" required
                           class="rounded border-gray-300 text-sm" />
                    <input v-model="extForm.additional_days" type="number" min="1" required
                           class="rounded border-gray-300 text-sm" placeholder="Días" />
                    <input v-model="extForm.additional_amount" type="number" step="0.01" min="0" required
                           class="rounded border-gray-300 text-sm" placeholder="Monto adicional" />
                    <button class="bg-orange-600 text-white text-sm py-1.5 rounded">Registrar extensión</button>
                </form>
                <textarea v-model="extForm.reason" rows="2"
                          class="w-full rounded border-gray-300 text-sm mt-2" placeholder="Motivo / observaciones..."></textarea>

                <div v-if="reservation.extensions?.length" class="mt-3 text-xs text-gray-500">
                    <p v-for="e in reservation.extensions" :key="e.id">
                        +{{ e.additional_days }} días → {{ e.new_end_date }} ({{ fmt(e.additional_amount) }})
                    </p>
                </div>
            </div>

            <!-- Daños y CxP -->
            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">🛠 Daños registrados</h3>
                    <ul class="text-sm space-y-2">
                        <li v-for="d in reservation.damages" :key="d.id" class="flex justify-between border-b pb-1">
                            <span>{{ d.zone }} — {{ d.description }}</span>
                            <span class="text-xs">{{ fmt(d.amount_charged) }} ({{ d.status }})</span>
                        </li>
                        <li v-if="!reservation.damages?.length" class="text-gray-400 text-xs">Sin daños registrados.</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-3">💵 CxP a subarrendador</h3>
                    <ul class="text-sm space-y-2">
                        <li v-for="p in reservation.payables" :key="p.id" class="flex justify-between border-b pb-1">
                            <span>{{ p.status }}</span>
                            <span class="font-semibold">{{ fmt(p.amount) }}</span>
                        </li>
                        <li v-if="!reservation.payables?.length" class="text-gray-400 text-xs">Sin cuentas por pagar.</li>
                    </ul>
                </div>
            </div>

            <!-- Cancelación -->
            <div v-if="reservation.cancellation"
                 class="lg:col-span-3 bg-red-50 border border-red-200 rounded-xl p-5">
                <h3 class="font-semibold text-red-700 mb-2">✖️ Cancelación</h3>
                <div class="text-sm grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div><p class="text-xs text-gray-500">Motivo</p>{{ reservation.cancellation.reason }}</div>
                    <div><p class="text-xs text-gray-500">Anticipación</p>{{ reservation.cancellation.hours_anticipation }} h</div>
                    <div><p class="text-xs text-gray-500">% Devolución</p>{{ reservation.cancellation.refund_percentage }}%</div>
                    <div><p class="text-xs text-gray-500">Reembolso</p>{{ fmt(reservation.cancellation.amount_refunded) }}</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
