<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status:           { type: String  },
});

const form = useForm({
    email:    '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Iniciar sesión — Sunset RentCar" />

        <!-- Mensaje de estado (ej: password reset) -->
        <div v-if="status"
             class="mb-5 px-4 py-3 rounded-xl text-sm font-medium text-green-300 border border-green-500/30"
             style="background:rgba(34,197,94,0.08);">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-widest mb-2"
                       style="color:rgba(251,191,36,0.7);">
                    Correo electrónico
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" style="color:rgba(251,191,36,0.4);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required autofocus autocomplete="username"
                        placeholder="admin@sunsetrentcar.com"
                        class="w-full pl-11 pr-4 py-3.5 rounded-xl text-sm text-white placeholder-amber-100/20 outline-none transition-all"
                        :class="form.errors.email ? 'border border-red-500/50' : 'border border-amber-400/15 focus:border-amber-400/50 focus:ring-2 focus:ring-amber-400/10'"
                        style="background:#131009; font-family:'Inter',sans-serif;"
                    />
                </div>
                <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-400">{{ form.errors.email }}</p>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-widest mb-2"
                       style="color:rgba(251,191,36,0.7);">
                    Contraseña
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4" style="color:rgba(251,191,36,0.4);" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full pl-11 pr-4 py-3.5 rounded-xl text-sm text-white placeholder-amber-100/20 outline-none transition-all"
                        :class="form.errors.password ? 'border border-red-500/50' : 'border border-amber-400/15 focus:border-amber-400/50 focus:ring-2 focus:ring-amber-400/10'"
                        style="background:#131009; font-family:'Inter',sans-serif;"
                    />
                </div>
                <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">{{ form.errors.password }}</p>
            </div>

            <!-- Recordarme + olvidé contraseña -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <span class="relative inline-flex h-5 w-9 shrink-0">
                        <input v-model="form.remember" type="checkbox" class="peer sr-only"/>
                        <span class="absolute inset-0 rounded-full transition-colors duration-200"
                              style="background:rgba(251,191,36,0.1);"
                              :style="form.remember ? 'background:rgba(251,191,36,0.8);' : ''"></span>
                        <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200"
                              :class="form.remember ? 'translate-x-4' : ''"></span>
                    </span>
                    <span class="text-xs font-medium" style="color:rgba(251,191,36,0.5);">Recordarme</span>
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')"
                    class="text-xs transition-colors"
                    style="color:rgba(251,191,36,0.4);"
                    onmouseover="this.style.color='rgba(251,191,36,0.8)'"
                    onmouseout="this.style.color='rgba(251,191,36,0.4)'">
                    ¿Olvidaste tu contraseña?
                </Link>
            </div>

            <!-- Botón submit -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full flex items-center justify-center gap-3 py-3.5 rounded-xl font-black text-sm tracking-wide text-black transition-all"
                :class="form.processing ? 'opacity-60 cursor-not-allowed' : 'hover:scale-[1.02] hover:shadow-xl active:scale-100'"
                style="background: linear-gradient(135deg,#fbbf24,#fb923c); box-shadow: 0 0 24px rgba(251,191,36,0.2); font-family:'Syne',sans-serif;">
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ form.processing ? 'Ingresando…' : 'Ingresar al Panel' }}
            </button>
        </form>
    </GuestLayout>
</template>
