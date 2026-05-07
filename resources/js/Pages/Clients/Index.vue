<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    clients: Object,   // paginado
    filters: Object,
});

// --- Modal ---
const showModal  = ref(false);
const editingId  = ref(null);

const form = useForm({
    first_name: '', last_name: '', identity_number: '', phone: '',
    whatsapp: '', email: '', nationality: 'HN',
    client_type: 'nacional', status: 'normal',
    block_reason: '', notes: '', source: 'WhatsApp', city: '', country: 'Honduras',
    license_photo: null, identity_photo: null,
    selfie_with_id_photo: null, face_photo: null,
});

// Vista previa local de las fotos antes de enviar
const previews = ref({
    license_photo: null, identity_photo: null,
    selfie_with_id_photo: null, face_photo: null,
});

// Fotos ya guardadas en el servidor (cuando se edita)
const existing = ref({
    license_photo: null, identity_photo: null,
    selfie_with_id_photo: null, face_photo: null,
});

const onPickPhoto = (field, e) => {
    const f = e.target.files[0];
    if (!f) return;
    form[field] = f;
    previews.value[field] = URL.createObjectURL(f);
};

const photoUrl = (field) => previews.value[field] ?? (existing.value[field] ? `/storage/${existing.value[field]}` : null);

const resetPhotoState = () => {
    Object.keys(previews.value).forEach(k => {
        if (previews.value[k]) URL.revokeObjectURL(previews.value[k]);
        previews.value[k] = null;
        existing.value[k] = null;
    });
};

const openCreate = () => {
    form.reset();
    resetPhotoState();
    editingId.value = null;
    showModal.value = true;
};

const openEdit = (c) => {
    form.reset();
    resetPhotoState();
    // Solo asignar campos de texto; ignorar las claves de file (irían como string)
    const photoFields = ['license_photo', 'identity_photo', 'selfie_with_id_photo', 'face_photo'];
    Object.keys(form).forEach(k => {
        if (photoFields.includes(k)) return;
        if (k in c) form[k] = c[k] ?? '';
    });
    form.block_reason = c.block_reason ?? '';
    photoFields.forEach(f => { existing.value[f] = c[f] ?? null; });
    editingId.value   = c.id;
    showModal.value   = true;
};

const submit = () => {
    const opts = {
        forceFormData: true,
        onSuccess: () => { showModal.value = false; resetPhotoState(); },
    };
    if (editingId.value) {
        // Method spoofing: PATCH con archivos requiere POST + campo _method
        form.transform((data) => ({ ...data, _method: 'patch' }))
            .post(route('clients.update', editingId.value), opts);
    } else {
        form.post(route('clients.store'), opts);
    }
};

const deleteClient = (c) => {
    if (confirm(`¿Eliminar a ${c.first_name} ${c.last_name}?`)) {
        router.delete(route('clients.destroy', c.id));
    }
};

// --- Filtros locales (el servidor ya filtra, aquí es solo UI) ---
const search      = ref(props.filters?.search ?? '');
const filterStatus = ref(props.filters?.status ?? '');

const applyFilters = () => {
    router.get(route('clients.index'), {
        search: search.value || undefined,
        status: filterStatus.value || undefined,
    }, { preserveState: true, replace: true });
};

// --- Badge helpers ---
const statusBadge = {
    normal:      'bg-gray-100 text-gray-700',
    vip:         'bg-purple-100 text-purple-800',
    observacion: 'bg-yellow-100 text-yellow-800',
    bloqueado:   'bg-red-100 text-red-800',
};
const statusLabel = {
    normal:      'Normal',
    vip:         'VIP ⭐',
    observacion: 'Observación ⚠️',
    bloqueado:   'Bloqueado 🚫',
};
const typeBadge = {
    nacional:    'bg-blue-50 text-blue-700',
    extranjero:  'bg-green-50 text-green-700',
    corporativo: 'bg-orange-50 text-orange-700',
};
</script>

<template>
    <Head title="CRM — Clientes" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">👥 CRM — Clientes</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ clients.total }} clientes registrados</p>
                </div>
                <button @click="openCreate"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    + Nuevo cliente
                </button>
            </div>
        </template>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3">
            <input v-model="search" @keyup.enter="applyFilters" type="text"
                placeholder="Buscar por nombre, identidad o teléfono..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-56 focus:outline-none focus:ring-2 focus:ring-red-500"/>
            <select v-model="filterStatus" @change="applyFilters"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                <option value="">Todos los estados</option>
                <option value="normal">Normal</option>
                <option value="vip">VIP</option>
                <option value="observacion">Observación</option>
                <option value="bloqueado">Bloqueado</option>
            </select>
            <button @click="applyFilters"
                class="px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-900 transition-colors">
                Buscar
            </button>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Cliente</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Contacto</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Tipo</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Estado</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-700">Ciudad</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="c in clients.data" :key="c.id" class="hover:bg-gray-50 transition-colors"
                        :class="c.status === 'bloqueado' ? 'bg-red-50' : ''">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ c.first_name }} {{ c.last_name }}</div>
                            <div class="text-gray-400 text-xs">{{ c.identity_number ?? 'Sin identidad' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            <div>📱 {{ c.whatsapp ?? c.phone ?? '—' }}</div>
                            <div class="text-xs text-gray-400">{{ c.email ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium capitalize" :class="typeBadge[c.client_type]">
                                {{ c.client_type }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusBadge[c.status]">
                                {{ statusLabel[c.status] }}
                            </span>
                            <div v-if="c.block_reason" class="text-xs text-red-500 mt-1 max-w-xs truncate">{{ c.block_reason }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ c.city ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 justify-end">
                                <button @click="openEdit(c)"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50">
                                    Editar
                                </button>
                                <button @click="deleteClient(c)"
                                    class="text-red-500 hover:text-red-700 text-xs font-medium px-2 py-1 rounded hover:bg-red-50">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="clients.data.length === 0">
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            No se encontraron clientes.
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div v-if="clients.last_page > 1" class="px-4 py-3 border-t border-gray-100 flex gap-2 flex-wrap">
                <Link v-for="p in clients.links" :key="p.label"
                    :href="p.url ?? '#'"
                    class="px-3 py-1 rounded text-sm border"
                    :class="p.active
                        ? 'bg-red-600 text-white border-red-600'
                        : 'border-gray-300 text-gray-600 hover:bg-gray-50'"
                    v-html="p.label"/>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">
                        {{ editingId ? 'Editar cliente' : 'Nuevo cliente' }}
                    </h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input v-model="form.first_name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                            <p v-if="form.errors.first_name" class="text-red-500 text-xs mt-1">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
                            <input v-model="form.last_name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">DNI / Pasaporte</label>
                            <input v-model="form.identity_number" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                            <input v-model="form.whatsapp" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <input v-model="form.phone" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                            <input v-model="form.email" type="email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de cliente *</label>
                            <select v-model="form.client_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="nacional">Nacional</option>
                                <option value="extranjero">Extranjero</option>
                                <option value="corporativo">Corporativo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                            <select v-model="form.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="normal">Normal</option>
                                <option value="vip">VIP</option>
                                <option value="observacion">Observación</option>
                                <option value="bloqueado">Bloqueado</option>
                            </select>
                        </div>
                        <div v-if="form.status === 'bloqueado'" class="col-span-2">
                            <label class="block text-sm font-medium text-red-700 mb-1">Motivo del bloqueo *</label>
                            <input v-model="form.block_reason" type="text" required
                                class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                            <p v-if="form.errors.block_reason" class="text-red-500 text-xs mt-1">{{ form.errors.block_reason }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                            <input v-model="form.city" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fuente</label>
                            <select v-model="form.source" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Web">Sitio Web</option>
                                <option value="Referido">Referido</option>
                                <option value="Presencial">Presencial</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notas internas</label>
                            <textarea v-model="form.notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                        </div>
                    </div>

                    <!-- KYC: 4 fotos -->
                    <div class="border-t pt-4">
                        <p class="text-sm font-semibold text-gray-800 mb-1">📸 Verificación de identidad (KYC)</p>
                        <p class="text-xs text-gray-500 mb-3">Las fotos se comprimen automáticamente al subirlas (máx ~300KB cada una).</p>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div v-for="slot in [
                                { field: 'identity_photo',       label: 'Documento (DNI / pasaporte)', icon: '🪪' },
                                { field: 'selfie_with_id_photo', label: 'Sosteniendo el documento',     icon: '🤳' },
                                { field: 'face_photo',           label: 'Facial (sin lentes ni gorra)', icon: '😊' },
                                { field: 'license_photo',        label: 'Licencia de conducir',          icon: '🚗' },
                            ]" :key="slot.field"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-2 text-center hover:border-red-400 transition-colors">
                                <p class="text-[11px] font-medium text-gray-700 mb-1">{{ slot.icon }} {{ slot.label }}</p>
                                <div v-if="photoUrl(slot.field)" class="relative">
                                    <img :src="photoUrl(slot.field)" class="w-full h-24 object-cover rounded" />
                                    <label class="block mt-1 cursor-pointer text-[11px] text-blue-600 hover:underline">
                                        Reemplazar
                                        <input type="file" accept="image/*" capture="environment" class="hidden"
                                               @change="onPickPhoto(slot.field, $event)" />
                                    </label>
                                </div>
                                <label v-else class="block cursor-pointer">
                                    <div class="h-24 flex items-center justify-center text-gray-400 text-xs">
                                        + Tocar para subir
                                    </div>
                                    <input type="file" accept="image/*" capture="environment" class="hidden"
                                           @change="onPickPhoto(slot.field, $event)" />
                                </label>
                            </div>
                        </div>
                        <p v-if="form.errors.identity_photo || form.errors.selfie_with_id_photo || form.errors.face_photo || form.errors.license_photo"
                           class="text-red-500 text-xs mt-2">
                            {{ form.errors.identity_photo || form.errors.selfie_with_id_photo || form.errors.face_photo || form.errors.license_photo }}
                        </p>
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
