<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    cancellations: Object,
});

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });

const sigForm = useForm({ signature: null });
const upload = (id, e) => {
    sigForm.signature = e.target.files[0];
    sigForm.post(route('cancellations.sign', id), {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => { e.target.value = ''; sigForm.reset('signature'); },
    });
};

const statusBadge = (s) => ({
    pendiente_firma: 'bg-amber-100 text-amber-800',
    firmado:         'bg-emerald-100 text-emerald-800',
    reembolsado:     'bg-blue-100 text-blue-800',
    cerrado:         'bg-gray-200 text-gray-700',
}[s] ?? 'bg-gray-100');
</script>

<template>
    <Head title="Cancelaciones" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-900">✖️ Cancelaciones y devoluciones</h2>
        </template>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Reserva</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Motivo</th>
                        <th class="px-4 py-3 text-center">Anticipación</th>
                        <th class="px-4 py-3 text-right">% Devol.</th>
                        <th class="px-4 py-3 text-right">Reembolso</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="c in cancellations.data" :key="c.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">
                            <Link :href="route('reservations.show', c.reservation.id)" class="text-amber-600 hover:underline">
                                {{ c.reservation?.code }}
                            </Link>
                        </td>
                        <td class="px-4 py-3">{{ c.reservation?.client?.first_name }} {{ c.reservation?.client?.last_name }}</td>
                        <td class="px-4 py-3 text-xs">{{ c.reason }}</td>
                        <td class="px-4 py-3 text-center text-xs">{{ Number(c.hours_anticipation).toFixed(1) }} h</td>
                        <td class="px-4 py-3 text-right">{{ c.refund_percentage }}%</td>
                        <td class="px-4 py-3 text-right text-emerald-700 font-semibold">{{ fmt(c.amount_refunded) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs" :class="statusBadge(c.status)">{{ c.status }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <label v-if="c.status === 'pendiente_firma'" class="text-xs text-amber-600 cursor-pointer hover:underline">
                                + Subir firma
                                <input type="file" accept="image/*" class="hidden" @change="upload(c.id, $event)" />
                            </label>
                            <span v-else-if="c.signature_path" class="text-xs text-emerald-600">✓ firmado</span>
                        </td>
                    </tr>
                    <tr v-if="!cancellations.data.length">
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">Sin cancelaciones registradas.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
