<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sublessors: Object,
    filters: Object,
});

const showModal = ref(false);
const editingId = ref(null);
const search    = ref(props.filters?.search ?? '');

const form = useForm({
    name: '', identity_number: '', phone: '', whatsapp: '', email: '',
    address: '', city: '', bank_name: '', bank_account: '',
    payment_method: 'transferencia', default_share: 0, notes: '', is_active: true,
});

const openCreate = () => { form.reset(); editingId.value = null; showModal.value = true; };
const openEdit = (s) => {
    Object.keys(form).forEach(k => { if (k in s) form[k] = s[k] ?? form[k]; });
    editingId.value = s.id;
    showModal.value = true;
};

const submit = () => {
    if (editingId.value) {
        form.patch(route('sublessors.update', editingId.value), { onSuccess: () => showModal.value = false });
    } else {
        form.post(route('sublessors.store'), { onSuccess: () => showModal.value = false });
    }
};

const remove = (s) => {
    if (confirm(`¿Eliminar a ${s.name}?`)) router.delete(route('sublessors.destroy', s.id));
};

const apply = () => router.get(route('sublessors.index'), { search: search.value || undefined }, { preserveState: true, replace: true });

const fmt = (n) => 'L. ' + Number(n ?? 0).toLocaleString('es-HN', { minimumFractionDigits: 2 });
</script>

<template>
    <Head title="Subarrendadores" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">🤝 Subarrendadores</h2>
                <button @click="openCreate" class="px-4 py-2 rounded-lg text-white text-sm font-semibold" style="background:#0c0a07;">
                    + Nuevo subarrendador
                </button>
            </div>
        </template>

        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4 flex gap-3">
            <input v-model="search" @keyup.enter="apply" type="text"
                   placeholder="Buscar por nombre, teléfono, RTN..."
                   class="flex-1 rounded-lg border-gray-300 text-sm" />
            <button @click="apply" class="px-3 py-2 bg-gray-900 text-white text-sm rounded-lg">Filtrar</button>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Contacto</th>
                        <th class="px-4 py-3 text-center">Vehículos</th>
                        <th class="px-4 py-3 text-center">Rentas</th>
                        <th class="px-4 py-3 text-right">CxP pendiente</th>
                        <th class="px-4 py-3 text-right">CxP pagado</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="s in sublessors.data" :key="s.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ s.name }}
                            <p class="text-xs text-gray-400">{{ s.identity_number }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <p>{{ s.phone }}</p>
                            <p class="text-gray-500">{{ s.whatsapp }} · {{ s.email }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">{{ s.vehicles?.length ?? 0 }}</td>
                        <td class="px-4 py-3 text-center">{{ s.reservations_count ?? 0 }}</td>
                        <td class="px-4 py-3 text-right text-amber-700">{{ fmt(s.pending_amount) }}</td>
                        <td class="px-4 py-3 text-right text-emerald-700">{{ fmt(s.paid_amount) }}</td>
                        <td class="px-4 py-3 text-right text-xs space-x-2">
                            <button @click="openEdit(s)" class="text-amber-600 hover:underline">Editar</button>
                            <button @click="remove(s)" class="text-red-500 hover:underline">Eliminar</button>
                        </td>
                    </tr>
                    <tr v-if="!sublessors.data.length">
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">Sin subarrendadores registrados.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-2xl w-full p-6">
                <h3 class="font-semibold text-lg mb-4">{{ editingId ? 'Editar subarrendador' : 'Nuevo subarrendador' }}</h3>
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <input v-model="form.name" placeholder="Nombre completo *" required class="rounded border-gray-300" />
                    <input v-model="form.identity_number" placeholder="DNI / RTN" class="rounded border-gray-300" />
                    <input v-model="form.phone" placeholder="Teléfono" class="rounded border-gray-300" />
                    <input v-model="form.whatsapp" placeholder="WhatsApp" class="rounded border-gray-300" />
                    <input v-model="form.email" type="email" placeholder="Email" class="rounded border-gray-300" />
                    <input v-model="form.city" placeholder="Ciudad" class="rounded border-gray-300" />
                    <input v-model="form.address" placeholder="Dirección" class="md:col-span-2 rounded border-gray-300" />
                    <input v-model="form.bank_name" placeholder="Banco" class="rounded border-gray-300" />
                    <input v-model="form.bank_account" placeholder="No. cuenta" class="rounded border-gray-300" />
                    <select v-model="form.payment_method" class="rounded border-gray-300">
                        <option value="transferencia">Transferencia</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                    </select>
                    <div>
                        <label class="text-xs text-gray-500">% sobre tarifa</label>
                        <input v-model="form.default_share" type="number" min="0" max="100" step="0.01"
                               class="w-full rounded border-gray-300" />
                    </div>
                    <textarea v-model="form.notes" rows="2" placeholder="Notas..." class="md:col-span-2 rounded border-gray-300"></textarea>

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
