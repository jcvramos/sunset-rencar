<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    payables: Object,
    sublessors: Array,
    filters: Object,
    totals: Object,
});

const status = ref(props.filters?.status ?? '');
const sub    = ref(props.filters?.sublessor_id ?? '');

const apply = () => router.get(route('sublessors.payables.index'), {
    status: status.value || undefined,
    sublessor_id: sub.value || undefined,
}, { preserveState: true, replace: true });

const payForm = useForm({ payment_reference: '', notes: '' });
const payNow = (p) => {
    payForm.payment_reference = prompt(`Referencia de pago para CxP #${p.id}:`) ?? '';
    payForm.post(route('sublessors.payables.pay', p.id), { preserveScroll: true });
};

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });

const statusBadge = (s) => ({
    pendiente: 'bg-amber-100 text-amber-800',
    pagado:    'bg-emerald-100 text-emerald-800',
    reversado: 'bg-gray-200 text-gray-600',
    cancelado: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100');
</script>

<template>
    <Head title="Cuentas por pagar" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900">💵 Cuentas por pagar a subarrendadores</h2>
        </template>

        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs text-amber-700">Pendiente</p>
                <p class="text-2xl font-bold text-amber-700">{{ fmt(totals.pendiente) }}</p>
            </div>
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <p class="text-xs text-emerald-700">Pagado</p>
                <p class="text-2xl font-bold text-emerald-700">{{ fmt(totals.pagado) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex flex-wrap gap-3">
            <select v-model="status" @change="apply" class="rounded-lg border-gray-300 text-sm">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="pagado">Pagado</option>
                <option value="reversado">Reversado</option>
            </select>
            <select v-model="sub" @change="apply" class="rounded-lg border-gray-300 text-sm">
                <option value="">Todos los subarrendadores</option>
                <option v-for="s in sublessors" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Subarrendador</th>
                        <th class="px-4 py-3 text-left">Reserva</th>
                        <th class="px-4 py-3 text-left">Vehículo</th>
                        <th class="px-4 py-3 text-right">Monto</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-left">Vence / Pagado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="p in payables.data" :key="p.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ p.sublessor?.name }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ p.reservation?.code }}</td>
                        <td class="px-4 py-3 text-xs">{{ p.vehicle?.name }} ({{ p.vehicle?.plate }})</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ fmt(p.amount) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs" :class="statusBadge(p.status)">{{ p.status }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            <p v-if="p.due_date">vence {{ p.due_date }}</p>
                            <p v-if="p.paid_at" class="text-emerald-600">pagado {{ p.paid_at }}</p>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button v-if="p.status === 'pendiente'" @click="payNow(p)"
                                    class="text-xs bg-emerald-600 text-white px-3 py-1 rounded">
                                Pagar
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!payables.data.length">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">Sin cuentas por pagar.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
