<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const props = defineProps({
    vehicleTypes: Array,
    vehicles: Array,
});

// WhatsApp
const whatsappNumber = '50432480912';
const getWhatsappLink = (msg = '') => {
    const text = msg || '¡Hola! Quiero información sobre alquiler de vehículos.';
    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`;
};

// Navbar scroll
const scrolled = ref(false);
onMounted(() => {
    AOS.init({ duration: 800, once: true, easing: 'ease-out-cubic', offset: 60 });
    window.addEventListener('scroll', () => { scrolled.value = window.scrollY > 60; });
});

// ── Widget de búsqueda ──────────────────────────────────────────────────────
const search = ref({ location: '', pickupDate: '', returnDate: '' });
const today = new Date().toISOString().split('T')[0];

const searchWhatsapp = () => {
    const loc  = search.value.location   || 'No especificado';
    const from = search.value.pickupDate || 'No especificada';
    const to   = search.value.returnDate || 'No especificada';
    const msg  = `¡Hola! Quiero cotizar un vehículo 🚗\n\n📍 *Lugar de recogida:* ${loc}\n📅 *Fecha de recogida:* ${from}\n📅 *Fecha de entrega:* ${to}\n\n¿Tienen disponibilidad?`;
    window.open(getWhatsappLink(msg), '_blank');
};

// ── Catálogo ────────────────────────────────────────────────────────────────
const catalogo = [
    {
        name:    'Chevrolet Colorado',
        type:    'Pickup',
        emoji:   '🛻',
        seats:   5,
        img:     '/img/ChevroletColorado-1.png',
        tags:    ['4×4', 'Doble Cabina', 'Ideal aventura'],
        whatsapp: '¡Hola! Me interesa el Chevrolet Colorado. ¿Está disponible?',
    },
    {
        name:    'Kia Sorento',
        type:    'Camioneta 3 Filas',
        emoji:   '🚐',
        seats:   7,
        img:     '/img/klipartz.com(1).png',
        tags:    ['7 Pasajeros', '3 Filas', 'Familiar'],
        whatsapp: '¡Hola! Me interesa el Kia Sorento de 7 pasajeros. ¿Está disponible?',
    },
    {
        name:    'Kia Sorento Sport',
        type:    'Camioneta 2 Filas',
        emoji:   '🚙',
        seats:   5,
        img:     '/img/KiaSorento-1.png',
        tags:    ['5 Pasajeros', 'SUV', 'Premium'],
        whatsapp: '¡Hola! Me interesa el Kia Sorento Sport. ¿Está disponible?',
    },
    {
        name:    'Jeep Patriot',
        type:    'Turismo',
        emoji:   '🚗',
        seats:   5,
        img:     '/img/pat4.png',
        tags:    ['5 Pasajeros', 'SUV Compacto', 'Económico'],
        whatsapp: '¡Hola! Me interesa el Jeep Patriot. ¿Está disponible?',
    },
];

const activeType = ref(null);
const filtered = computed(() =>
    activeType.value ? catalogo.filter(v => v.type === activeType.value) : catalogo
);
const uniqueTypes = [...new Set(catalogo.map(v => v.type))];

// ── Servicios ───────────────────────────────────────────────────────────────
const servicios = [
    { icon: '🧑‍✈️', title: 'Servicio con Conductor',   desc: 'Conductor profesional disponible para tus viajes.' },
    { icon: '✈️',  title: 'Entrega en Aeropuerto',     desc: 'Te recibimos y entregamos el vehículo en el aeropuerto.' },
    { icon: '🛟',  title: 'Asistencia 24/7',            desc: 'Soporte en carretera disponible las 24 horas del día.' },
    { icon: '🗺️',  title: 'GPS Incluido',               desc: 'Navegación integrada para que nunca te pierdas.' },
    { icon: '🛡️',  title: 'Seguro Completo',             desc: 'Viaja tranquilo con cobertura total del vehículo.' },
    { icon: '🚿',  title: 'Lavado Gratis',               desc: 'Vehículo entregado limpio y en perfectas condiciones.' },
];

// ── Stats ───────────────────────────────────────────────────────────────────
const stats = [
    { value: '24/7', label: 'Disponibilidad' },
    { value: '6+',   label: 'Tipos de vehículo' },
    { value: '100%', label: 'Seguro incluido' },
    { value: '⭐',   label: 'Clientes satisfechos' },
];
</script>

<template>
    <Head title="Sunset RentCar — Alquiler de Vehículos en Honduras" />

    <!-- ═══════════════════════════════ NAVBAR ═══════════════════════════════ -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-500"
        :class="scrolled ? 'bg-black/95 backdrop-blur-md shadow-lg shadow-black/50' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-20">

            <!-- Logo real -->
            <a href="#inicio">
                <img src="/img/logo.png" alt="Sunset RentCar"
                    class="h-14 w-auto"
                    style="filter: drop-shadow(0 0 12px rgba(255,215,0,0.7)) drop-shadow(0 2px 4px rgba(0,0,0,0.9)) brightness(1.15)"/>
            </a>

            <!-- Nav links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#flota"      class="text-gray-300 hover:text-yellow-400 transition-colors">Flota</a>
                <a href="#servicios"  class="text-gray-300 hover:text-yellow-400 transition-colors">Servicios</a>
                <a href="#contacto"   class="text-gray-300 hover:text-yellow-400 transition-colors">Contacto</a>
                <Link :href="route('login')"
                    class="px-4 py-2 border border-gray-600 hover:border-yellow-400 text-gray-300 hover:text-yellow-400 rounded-lg transition-all text-sm">
                    Panel Interno
                </Link>
                <a :href="getWhatsappLink()" target="_blank"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-400 text-white font-bold rounded-lg transition-all hover:scale-105 shadow-lg shadow-green-900/30">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    WhatsApp
                </a>
            </nav>

            <!-- Mobile: solo WhatsApp -->
            <a :href="getWhatsappLink()" target="_blank"
                class="md:hidden flex items-center gap-1.5 px-3 py-2 bg-green-500 text-white text-sm font-bold rounded-lg">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
                WhatsApp
            </a>
        </div>
    </header>

    <!-- ═══════════════════════════════ HERO ═══════════════════════════════ -->
    <section id="inicio" class="relative min-h-screen bg-black overflow-hidden flex flex-col justify-center">

        <!-- ── Video de fondo ── -->
        <video autoplay muted loop playsinline
            class="absolute inset-0 w-full h-full object-cover pointer-events-none"
            style="opacity: 0.45;">
            <source src="/img/12127397_3840_2160_30fps.mp4" type="video/mp4">
            <source src="/img/6157785-hd_1920_1080_30fps.mp4" type="video/mp4">
        </video>

        <!-- Overlays de profundidad -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/40 to-black/80 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-transparent to-black/60 pointer-events-none"></div>

        <!-- ── Contenido centrado ── -->
        <div class="relative z-10 w-full max-w-6xl mx-auto px-4 sm:px-6 pt-32 pb-20 flex flex-col items-center text-center">

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 bg-yellow-400/10 border border-yellow-400/40
                        text-yellow-400 text-xs font-bold px-5 py-2 rounded-full mb-8 uppercase tracking-widest
                        backdrop-blur-sm"
                data-aos="fade-down">
                🌅 Sunset RentCar — Honduras
            </div>

            <!-- Titular principal -->
            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black text-white leading-none mb-5 tracking-tight"
                data-aos="fade-up" data-aos-delay="80">
                RENTA TU AUTO
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-yellow-400 to-orange-400 mt-1">
                    EN HONDURAS
                </span>
            </h1>

            <p class="text-gray-300 text-lg sm:text-xl mb-12 max-w-xl leading-relaxed"
                data-aos="fade-up" data-aos-delay="160">
                Flota moderna · Entrega en aeropuerto · Atención 24/7 · Seguro incluido
            </p>

            <!-- ══════════════════════════════════════════════════════
                 WIDGET DE BÚSQUEDA — estilo Sixt / RentalCars
                 ══════════════════════════════════════════════════════ -->
            <div class="w-full max-w-4xl" data-aos="fade-up" data-aos-delay="260">

                <!-- Tarjeta glassmorphism con borde dorado sutil -->
                <div class="relative bg-black/70 backdrop-blur-xl border border-yellow-400/20 rounded-2xl
                            shadow-2xl shadow-black/60 overflow-hidden">

                    <!-- Línea dorada superior -->
                    <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-yellow-400 to-transparent"></div>

                    <!-- Encabezado del widget -->
                    <div class="px-6 pt-5 pb-3 border-b border-white/5">
                        <p class="text-yellow-400 text-xs font-bold uppercase tracking-widest">
                            Consulta disponibilidad ahora
                        </p>
                    </div>

                    <!-- Campos -->
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                            <!-- Lugar de recogida -->
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 text-left">
                                    📍 Lugar de recogida
                                </label>
                                <div class="relative">
                                    <input v-model="search.location" type="text"
                                        placeholder="Ej: San Pedro Sula, Aeropuerto..."
                                        class="w-full bg-white/5 border border-white/10 hover:border-yellow-400/40
                                               focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20
                                               text-white placeholder-gray-500 rounded-xl px-4 py-3.5 text-sm
                                               outline-none transition-all"/>
                                </div>
                            </div>

                            <!-- Fecha de recogida -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 text-left">
                                    📅 Fecha de recogida
                                </label>
                                <input v-model="search.pickupDate" type="date" :min="today"
                                    class="w-full bg-white/5 border border-white/10 hover:border-yellow-400/40
                                           focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20
                                           text-white rounded-xl px-4 py-3.5 text-sm
                                           outline-none transition-all [color-scheme:dark]"/>
                            </div>

                            <!-- Fecha de entrega -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 text-left">
                                    🏁 Fecha de entrega
                                </label>
                                <input v-model="search.returnDate" type="date" :min="search.pickupDate || today"
                                    class="w-full bg-white/5 border border-white/10 hover:border-yellow-400/40
                                           focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/20
                                           text-white rounded-xl px-4 py-3.5 text-sm
                                           outline-none transition-all [color-scheme:dark]"/>
                            </div>
                        </div>

                        <!-- Botón de búsqueda -->
                        <button @click="searchWhatsapp"
                            class="mt-4 w-full flex items-center justify-center gap-3
                                   bg-yellow-400 hover:bg-yellow-300 active:bg-yellow-500
                                   text-black font-black text-base rounded-xl py-4
                                   transition-all hover:scale-[1.02] hover:shadow-xl hover:shadow-yellow-400/30
                                   active:scale-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                            Consultar disponibilidad por WhatsApp
                        </button>
                    </div>

                    <!-- Trust signals (estilo RentalCars) -->
                    <div class="px-6 py-3 border-t border-white/5 flex flex-wrap items-center justify-center gap-x-6 gap-y-1">
                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span class="text-green-400">✓</span> Sin cargos ocultos
                        </span>
                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span class="text-green-400">✓</span> Respuesta inmediata
                        </span>
                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span class="text-green-400">✓</span> Seguro incluido
                        </span>
                        <span class="text-xs text-gray-500 flex items-center gap-1.5">
                            <span class="text-green-400">✓</span> Entrega en aeropuerto
                        </span>
                    </div>
                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="mt-14 flex flex-col items-center gap-2 text-gray-600 text-xs">
                <div class="w-px h-10 bg-gradient-to-b from-transparent to-yellow-400/50 animate-pulse"></div>
                <span class="tracking-widest uppercase text-[10px]">scroll</span>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ STATS BAR ═══════════════════════════════ -->
    <section class="bg-yellow-400 py-8">
        <div class="max-w-4xl mx-auto px-6 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            <div v-for="s in stats" :key="s.label" data-aos="fade-up">
                <div class="text-3xl font-black text-black">{{ s.value }}</div>
                <div class="text-sm text-yellow-900 font-medium mt-1">{{ s.label }}</div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ CATÁLOGO ═══════════════════════════════ -->
    <section id="flota" class="py-24 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-14" data-aos="fade-up">
                <span class="text-yellow-400 font-bold text-xs uppercase tracking-[0.3em]">Nuestra flota</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">
                    Vehículos Disponibles
                </h2>
                <p class="text-gray-500 mt-4 max-w-lg mx-auto">
                    Flota propia y subarrendada, mantenida al máximo estándar para tu comodidad y seguridad.
                </p>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-2 justify-center mb-10" data-aos="fade-up">
                <button @click="activeType = null"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-all"
                    :class="!activeType
                        ? 'bg-yellow-400 text-black border-yellow-400'
                        : 'border-gray-700 text-gray-400 hover:border-yellow-400/50 hover:text-yellow-400'">
                    Todos
                </button>
                <button v-for="t in uniqueTypes" :key="t" @click="activeType = t"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-all"
                    :class="activeType === t
                        ? 'bg-yellow-400 text-black border-yellow-400'
                        : 'border-gray-700 text-gray-400 hover:border-yellow-400/50 hover:text-yellow-400'">
                    {{ t }}
                </button>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="(v, i) in filtered" :key="v.name"
                    data-aos="fade-up" :data-aos-delay="i * 80"
                    class="group bg-zinc-900 border border-zinc-800 hover:border-yellow-400/50 rounded-2xl overflow-hidden transition-all hover:-translate-y-2 hover:shadow-2xl hover:shadow-yellow-400/10">

                    <!-- Imagen del auto -->
                    <div class="relative bg-gradient-to-b from-zinc-800 to-zinc-900 pt-8 px-4 pb-2 flex items-end justify-center h-48 overflow-hidden">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom,_rgba(234,179,8,0.08)_0%,transparent_70%)]"></div>
                        <img :src="v.img" :alt="v.name"
                            class="h-36 w-auto object-contain relative z-10 drop-shadow-2xl
                                   group-hover:scale-105 transition-transform duration-500 filter brightness-105"/>
                        <!-- Tipo badge -->
                        <div class="absolute top-3 left-3 bg-yellow-400 text-black text-xs font-black px-2 py-1 rounded-full">
                            {{ v.emoji }} {{ v.type }}
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-5">
                        <h3 class="text-white font-bold text-xl">{{ v.name }}</h3>
                        <p class="text-gray-500 text-sm mt-1">👥 {{ v.seats }} pasajeros</p>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-1 mt-3">
                            <span v-for="tag in v.tags" :key="tag"
                                class="text-xs px-2 py-1 bg-zinc-800 text-gray-400 rounded-full border border-zinc-700">
                                {{ tag }}
                            </span>
                        </div>

                        <!-- CTA -->
                        <a :href="getWhatsappLink(v.whatsapp)" target="_blank"
                            class="mt-5 w-full flex items-center justify-center gap-2 py-3
                                   bg-green-500 hover:bg-green-400 text-white font-bold rounded-xl
                                   transition-all text-sm group-hover:shadow-lg group-hover:shadow-green-900/30">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                            Consultar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ SERVICIOS ═══════════════════════════════ -->
    <section id="servicios" class="py-24 bg-black relative overflow-hidden">
        <!-- Decoración dorada -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/50 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-yellow-400 font-bold text-xs uppercase tracking-[0.3em]">Lo que incluimos</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">Nuestros Servicios</h2>
                <p class="text-gray-500 mt-4 max-w-lg mx-auto">
                    Cada renta viene con beneficios que hacen tu experiencia más cómoda y segura.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div v-for="(s, i) in servicios" :key="s.title"
                    data-aos="fade-up" :data-aos-delay="(i % 3) * 100"
                    class="flex items-start gap-4 p-6 rounded-2xl border border-zinc-800 hover:border-yellow-400/40
                           bg-zinc-950 hover:bg-zinc-900 transition-all group">
                    <div class="text-4xl shrink-0 group-hover:scale-110 transition-transform">{{ s.icon }}</div>
                    <div>
                        <h3 class="text-white font-bold text-base mb-1">{{ s.title }}</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ s.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ TIPOS DE VEHÍCULO ═══════════════════════════════ -->
    <section class="py-20 bg-zinc-950">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12" data-aos="fade-up">
                <span class="text-yellow-400 font-bold text-xs uppercase tracking-[0.3em]">Flota completa</span>
                <h2 class="text-4xl font-black text-white mt-3">6 Tipos de Vehículo</h2>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                <a v-for="(type, i) in vehicleTypes" :key="type.id"
                    :href="getWhatsappLink(`¡Hola! Me interesa rentar: ${type.name}. ¿Tienen disponibilidad?`)"
                    target="_blank"
                    data-aos="zoom-in" :data-aos-delay="i * 60"
                    class="group flex flex-col items-center gap-2 p-4 rounded-xl border border-zinc-800
                           hover:border-yellow-400/50 hover:bg-yellow-400/5 transition-all text-center cursor-pointer">
                    <span class="text-4xl group-hover:scale-110 transition-transform">{{ type.emoji }}</span>
                    <span class="text-xs font-semibold text-gray-400 group-hover:text-yellow-400 transition-colors leading-tight">{{ type.name }}</span>
                    <span class="text-xs text-zinc-600">{{ type.seats }}p</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ CONTACTO + MAPA ═══════════════════════════════ -->
    <section id="contacto" class="bg-zinc-950 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-yellow-400/40 to-transparent"></div>

        <!-- Encabezado -->
        <div class="max-w-7xl mx-auto px-6 pt-20 pb-12 text-center" data-aos="fade-up">
            <span class="text-yellow-400 font-bold text-xs uppercase tracking-[0.3em]">Encuéntranos</span>
            <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">Contáctanos</h2>
            <p class="text-gray-500 mt-3 max-w-lg mx-auto">
                Visítanos en nuestra oficina o escríbenos directamente. Estamos listos para atenderte.
            </p>
        </div>

        <!-- Grid: info izquierda + mapa derecha -->
        <div class="max-w-7xl mx-auto px-6 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                <!-- ── Panel de información ── -->
                <div class="flex flex-col gap-4" data-aos="fade-right">

                    <!-- Dirección -->
                    <div class="bg-black/60 border border-zinc-800 hover:border-yellow-400/30 rounded-2xl p-6 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-yellow-400/10 border border-yellow-400/20 flex items-center justify-center shrink-0 group-hover:bg-yellow-400/20 transition-colors">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-yellow-400 text-xs font-bold uppercase tracking-widest mb-1">Dirección</p>
                                <p class="text-white font-semibold leading-snug">Barrio Paz Barahona</p>
                                <p class="text-gray-400 text-sm mt-0.5">Avenida Circunvalación, Plaza Nivel 1,<br>2do Nivel · Local #10</p>
                                <p class="text-gray-500 text-sm">San Pedro Sula, Cortés · Honduras</p>
                            </div>
                        </div>
                    </div>

                    <!-- Teléfono / WhatsApp -->
                    <div class="bg-black/60 border border-zinc-800 hover:border-yellow-400/30 rounded-2xl p-6 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center shrink-0 group-hover:bg-green-500/20 transition-colors">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-green-400 text-xs font-bold uppercase tracking-widest mb-1">WhatsApp / Teléfono</p>
                                <p class="text-white font-semibold">+504 3248-0912</p>
                                <a :href="getWhatsappLink('¡Hola! Quiero información sobre alquiler de vehículos.')"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-green-500 hover:bg-green-400
                                           text-white text-sm font-bold rounded-xl transition-all hover:scale-105">
                                    Escribir ahora →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="bg-black/60 border border-zinc-800 hover:border-yellow-400/30 rounded-2xl p-6 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-yellow-400/10 border border-yellow-400/20 flex items-center justify-center shrink-0 group-hover:bg-yellow-400/20 transition-colors">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-yellow-400 text-xs font-bold uppercase tracking-widest mb-1">Correo Electrónico</p>
                                <a href="mailto:info@sunsetrentcar.com"
                                    class="text-white font-semibold hover:text-yellow-400 transition-colors">
                                    info@sunsetrentcar.com
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Horario -->
                    <div class="bg-black/60 border border-zinc-800 hover:border-yellow-400/30 rounded-2xl p-6 transition-colors group">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-yellow-400/10 border border-yellow-400/20 flex items-center justify-center shrink-0 group-hover:bg-yellow-400/20 transition-colors">
                                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-yellow-400 text-xs font-bold uppercase tracking-widest mb-2">Horario de Atención</p>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between gap-8">
                                        <span class="text-gray-400">Lunes – Viernes</span>
                                        <span class="text-white font-medium">07:00 AM – 10:00 PM</span>
                                    </div>
                                    <div class="flex justify-between gap-8">
                                        <span class="text-gray-400">Sábados y Domingos</span>
                                        <span class="text-white font-medium">Con cita previa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Mapa de Google Maps ── -->
                <div class="rounded-2xl overflow-hidden border border-zinc-800 min-h-[420px] lg:min-h-0 relative"
                    data-aos="fade-left">
                    <!-- Overlay superior con nombre de lugar -->
                    <div class="absolute top-0 left-0 right-0 z-10 bg-gradient-to-b from-black/80 to-transparent px-5 py-4 pointer-events-none">
                        <p class="text-yellow-400 text-xs font-bold uppercase tracking-widest">📍 Nuestra Ubicación</p>
                        <p class="text-white text-sm font-semibold mt-0.5">Sunset RentCar · San Pedro Sula</p>
                    </div>

                    <iframe
                        title="Sunset RentCar — San Pedro Sula, Honduras"
                        class="w-full h-full min-h-[420px]"
                        style="border:0; filter: invert(90%) hue-rotate(180deg) brightness(0.85) contrast(1.1) saturate(0.7);"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=Barrio+Paz+Barahona,+Avenida+Circunvalacion,+San+Pedro+Sula,+Cortes,+Honduras&z=15&output=embed">
                    </iframe>

                    <!-- Botón abrir en Google Maps -->
                    <a href="https://maps.google.com/?q=Barrio+Paz+Barahona,+Avenida+Circunvalacion,+San+Pedro+Sula,+Honduras"
                        target="_blank" rel="noopener noreferrer"
                        class="absolute bottom-4 right-4 z-10 flex items-center gap-2 bg-black/80 backdrop-blur-sm
                               border border-yellow-400/40 text-yellow-400 hover:bg-yellow-400 hover:text-black
                               text-xs font-bold px-3 py-2 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Abrir en Maps
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ GALERÍA ═══════════════════════════════ -->
    <section class="py-0 bg-black overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 pb-24">
            <div class="text-center mb-14" data-aos="fade-up">
                <span class="text-yellow-400 font-bold text-xs uppercase tracking-[0.3em]">Experiencia de viaje</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">Vive el Camino</h2>
                <p class="text-gray-500 mt-4 max-w-lg mx-auto">
                    Cada ruta es una experiencia. Nuestros vehículos te llevan a donde quieras ir.
                </p>
            </div>

            <!-- Grid masonry de fotos -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" data-aos="fade-up" data-aos-delay="100">

                <!-- Foto grande izquierda -->
                <div class="md:col-span-2 md:row-span-2 relative group overflow-hidden rounded-2xl h-72 md:h-auto" style="min-height: 400px;">
                    <img src="/img/pexels-mikebirdy-4003121.jpg" alt="Vehículo Sunset RentCar"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <span class="text-yellow-400 font-bold text-xs uppercase tracking-widest">Flota Premium</span>
                        <h3 class="text-white font-black text-2xl mt-1">Vehículos de Alto Estándar</h3>
                    </div>
                </div>

                <!-- Foto superior derecha -->
                <div class="relative group overflow-hidden rounded-2xl h-56">
                    <img src="/img/pexels-vyacheslav-bobin-105199946-13033349.jpg" alt="Carretera Honduras"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="text-yellow-400 font-bold text-xs uppercase tracking-widest">Aventura</span>
                        <h3 class="text-white font-bold text-base mt-0.5">Rutas por Honduras</h3>
                    </div>
                </div>

                <!-- Foto inferior derecha -->
                <div class="relative group overflow-hidden rounded-2xl h-56">
                    <img src="/img/pexels-alfonso-romo-2155321859-35153444.jpg" alt="Viaje en familia"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="text-yellow-400 font-bold text-xs uppercase tracking-widest">Comodidad</span>
                        <h3 class="text-white font-bold text-base mt-0.5">Viajes en Familia</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════ FOOTER ═══════════════════════════════ -->
    <footer class="bg-zinc-950 border-t border-zinc-900 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-center md:text-left">
                    <img src="/img/logo.png" alt="Sunset RentCar" class="h-16 w-auto mx-auto md:mx-0 mb-3"
                        style="filter: drop-shadow(0 0 12px rgba(255,215,0,0.5)) brightness(1.15)"/>
                    <p class="text-gray-600 text-sm">San Pedro Sula, Cortés, Honduras</p>
                    <p class="text-gray-600 text-sm mt-1">
                        Desarrollado por
                        <span class="text-yellow-400">JCVR Digital Solutions</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-6 text-sm justify-center">
                    <a href="#inicio"     class="text-gray-500 hover:text-yellow-400 transition-colors">Inicio</a>
                    <a href="#flota"      class="text-gray-500 hover:text-yellow-400 transition-colors">Flota</a>
                    <a href="#servicios"  class="text-gray-500 hover:text-yellow-400 transition-colors">Servicios</a>
                    <a href="#contacto"   class="text-gray-500 hover:text-yellow-400 transition-colors">Contacto</a>
                    <Link :href="route('login')" class="text-gray-500 hover:text-yellow-400 transition-colors">Admin</Link>
                </div>
            </div>
            <div class="border-t border-zinc-900 mt-8 pt-6 text-center text-xs text-zinc-700">
                © {{ new Date().getFullYear() }} Sunset RentCar. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Botón WhatsApp flotante -->
    <a :href="getWhatsappLink()" target="_blank"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-400 text-white rounded-full
               shadow-2xl shadow-green-900/50 flex items-center justify-center
               transition-all hover:scale-110 hover:rotate-6"
        title="Chatear por WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
        </svg>
    </a>
</template>

<style>
/* Fechas en inputs oscuros — quitar fondo blanco del picker nativo */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1) opacity(0.4);
    cursor: pointer;
}
input[type="date"]::-webkit-calendar-picker-indicator:hover {
    filter: invert(1) opacity(0.8);
}
</style>
