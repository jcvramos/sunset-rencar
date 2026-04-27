<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex" style="background:#080604; font-family:'Inter',sans-serif;">

        <!-- ── Panel izquierdo: imagen/branding (solo desktop) ────────────── -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col justify-between p-12">

            <!-- Imagen de fondo -->
            <div class="absolute inset-0">
                <img src="/img/pexels-mikebirdy-4003121.jpg" alt=""
                     class="w-full h-full object-cover opacity-40"/>
                <div class="absolute inset-0"
                     style="background: linear-gradient(135deg, rgba(8,6,4,0.85) 0%, rgba(8,6,4,0.5) 100%);"></div>
                <!-- Glow sunset bottom -->
                <div class="absolute inset-0 pointer-events-none"
                     style="background: radial-gradient(ellipse at 50% 110%, rgba(251,146,60,0.25) 0%, transparent 60%);"></div>
            </div>

            <!-- Logo -->
            <div class="relative z-10">
                <Link href="/">
                    <img src="/img/logo.png" alt="Sunset RentCar" class="h-16 w-auto"
                         style="filter: drop-shadow(0 0 16px rgba(251,191,36,0.8)) brightness(1.15);"/>
                </Link>
            </div>

            <!-- Copy central -->
            <div class="relative z-10">
                <h2 class="text-white font-black text-4xl leading-tight mb-4"
                    style="font-family:'Syne',sans-serif;">
                    Viaja en grande.<br>
                    <span class="text-transparent bg-clip-text"
                          style="background: linear-gradient(90deg,#fbbf24,#fb923c);">
                        Paga lo justo.
                    </span>
                </h2>
                <p class="text-amber-100/50 text-sm leading-relaxed max-w-xs">
                    Panel de administración de Sunset RentCar. Gestiona tu flota, clientes y reservaciones desde un solo lugar.
                </p>
            </div>

            <!-- Footer branding -->
            <div class="relative z-10">
                <p class="text-amber-100/25 text-xs">© {{ new Date().getFullYear() }} Sunset RentCar · San Pedro Sula, Honduras</p>
            </div>
        </div>

        <!-- ── Panel derecho: formulario ─────────────────────────────────── -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12 lg:px-16">

            <!-- Logo mobile -->
            <div class="lg:hidden mb-8">
                <Link href="/">
                    <img src="/img/logo.png" alt="Sunset RentCar" class="h-16 w-auto mx-auto"
                         style="filter: drop-shadow(0 0 16px rgba(251,191,36,0.7)) brightness(1.15);"/>
                </Link>
            </div>

            <!-- Card del formulario -->
            <div class="w-full max-w-sm">

                <!-- Cabecera -->
                <div class="mb-8">
                    <h1 class="text-white font-black text-3xl mb-2"
                        style="font-family:'Syne',sans-serif;">
                        Panel Interno
                    </h1>
                    <p class="text-amber-100/40 text-sm">Ingresa tus credenciales para continuar</p>
                </div>

                <!-- Slot con el formulario -->
                <slot />

                <!-- Volver a la landing -->
                <div class="mt-8 text-center">
                    <Link href="/"
                        class="text-amber-100/30 hover:text-amber-400 text-xs transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al sitio web
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
