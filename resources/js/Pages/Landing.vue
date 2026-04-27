<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref, computed } from 'vue';
import AOS from 'aos';
import 'aos/dist/aos.css';

const props = defineProps({
    vehicleTypes: Array,
    vehicles: Array,
});

// ── Idioma ──────────────────────────────────────────────────────────────────
const locale = ref('es');
const toggleLocale = () => { locale.value = locale.value === 'es' ? 'en' : 'es'; };

const i18n = {
    es: {
        // Meta
        pageTitle: 'Sunset RentCar — Alquiler de Vehículos en Honduras',

        // Announcement bar
        announcementBar: '✈️ Entrega en el Aeropuerto de San Pedro Sula · Atención 24/7 ·',
        announcementCta: 'Cotiza ahora →',

        // Nav
        navHowItWorks: 'Cómo funciona',
        navFleet:      'Flota',
        navServices:   'Servicios',
        navContact:    'Contacto',
        navAdmin:      'Panel Interno',
        navWhatsapp:   'WhatsApp',

        // Hero
        heroBadge:    '🌅 Sunset RentCar — Honduras',
        heroTitle1:   'RENTA TU AUTO',
        heroTitle2:   'EN HONDURAS',
        heroSubtitle: 'Flota moderna · Entrega en aeropuerto · Atención 24/7 · Seguro incluido',

        // Widget
        widgetLabel:          'Consulta disponibilidad ahora',
        widgetNote:           'Sin cargos por cotizar',
        widgetLocation:       'Lugar de recogida',
        widgetReturnLocation: 'Lugar de devolución',
        widgetLocationPh:     'Ej: Aeropuerto SAP, San Pedro Sula, Tela...',
        widgetPickupDate:     'Fecha recogida',
        widgetPickupTime:     'Hora recogida',
        widgetReturnDate:     'Fecha entrega',
        widgetReturnTime:     'Hora entrega',
        widgetDiffReturn:     'Devolver en lugar diferente',
        widgetCta:            'Consultar por WhatsApp',
        trustNoHidden:     '✓ Sin cargos ocultos',
        trustImmediate:    '✓ Respuesta inmediata',
        trustInsurance:    '✓ Seguro incluido',
        trustAirport:      '✓ Entrega en aeropuerto',

        // Stats
        statAvail:    'Disponibilidad',
        statTypes:    'Tipos de vehículo',
        statInsurance:'Seguro incluido',
        statClients:  'Clientes satisfechos',

        // How it works
        howBadge:    'Proceso simple',
        howTitle:    '¿Cómo Funciona?',
        howSubtitle: 'Renta un vehículo en 3 simples pasos. Sin complicaciones, sin sorpresas.',
        howCta:      'Comenzar mi reservación',
        steps: [
            { step:'01', icon:'🔍', title:'Elige tu Vehículo',    desc:'Explora nuestra flota y selecciona el auto ideal para tu viaje: pickup, SUV, familiar o compacto.' },
            { step:'02', icon:'💬', title:'Confirma por WhatsApp', desc:'Envíanos tus fechas y lugar de recogida. Recibirás confirmación y precio en minutos.' },
            { step:'03', icon:'🚗', title:'Recibe tu Auto',        desc:'Te entregamos el vehículo limpio, con seguro y listo para rodar. En aeropuerto o en oficina.' },
        ],

        // Fleet
        fleetBadge:    'Nuestra flota',
        fleetTitle:    'Vehículos Disponibles',
        fleetSubtitle: 'Flota propia y subarrendada, mantenida al máximo estándar para tu comodidad y seguridad.',
        fleetAll:      'Todos',
        fleetPassengers: 'pasajeros',
        fleetWhatsappCta:'Consultar por WhatsApp',
        vehicles: [
            { name:'Chevrolet Colorado', type:'Pickup',            emoji:'🛻', seats:5, img:'/img/ChevroletColorado-1.png',    tags:['4×4','Doble Cabina','Ideal aventura'], wa:'¡Hola! Me interesa el Chevrolet Colorado. ¿Está disponible?' },
            { name:'Kia Sorento',        type:'Camioneta 3 Filas', emoji:'🚐', seats:7, img:'/img/klipartz.com(1).png',         tags:['7 Pasajeros','3 Filas','Familiar'],    wa:'¡Hola! Me interesa el Kia Sorento de 7 pasajeros. ¿Está disponible?' },
            { name:'Kia Sorento Sport',  type:'Camioneta 2 Filas', emoji:'🚙', seats:5, img:'/img/KiaSorento-1.png',            tags:['5 Pasajeros','SUV','Premium'],         wa:'¡Hola! Me interesa el Kia Sorento Sport. ¿Está disponible?' },
            { name:'Jeep Patriot',       type:'Turismo',           emoji:'🚗', seats:5, img:'/img/pat4.png',                   tags:['5 Pasajeros','SUV Compacto','Económico'], wa:'¡Hola! Me interesa el Jeep Patriot. ¿Está disponible?' },
        ],

        // Services
        servicesBadge:    'Lo que incluimos',
        servicesTitle:    'Nuestros Servicios',
        servicesSubtitle: 'Cada renta viene con beneficios que hacen tu experiencia más cómoda y segura.',
        services: [
            { icon:'🧑‍✈️', title:'Servicio con Conductor',   desc:'Conductor profesional disponible para tus viajes.' },
            { icon:'✈️',   title:'Entrega en Aeropuerto',     desc:'Te recibimos y entregamos el vehículo en el aeropuerto.' },
            { icon:'🛟',   title:'Asistencia 24/7',            desc:'Soporte en carretera disponible las 24 horas del día.' },
            { icon:'🗺️',   title:'GPS Incluido',               desc:'Navegación integrada para que nunca te pierdas.' },
            { icon:'🛡️',   title:'Seguro Completo',             desc:'Viaja tranquilo con cobertura total del vehículo.' },
            { icon:'🚿',   title:'Lavado Gratis',               desc:'Vehículo entregado limpio y en perfectas condiciones.' },
        ],

        // Vehicle types
        typesBadge: 'Flota completa',
        typesTitle: '6 Tipos de Vehículo',

        // Testimonials
        testimonialsBadge:    'Lo que dicen nuestros clientes',
        testimonialsTitle:    'Experiencias Reales',
        testimonialsSubtitle: 'Cientos de viajeros ya confiaron en nosotros. Aquí te contamos sus experiencias.',
        testimonialsLeave:    '¿Ya rentaste con nosotros? Comparte tu experiencia',
        testimonialsLeaveCta: 'Dejar mi reseña →',
        testimonialsVerified: 'Verificado',
        testimonials: [
            { nombre:'Carlos Mejía',           lugar:'San Pedro Sula', estrellas:5, avatar:'CM', texto:'Excelente servicio. Me entregaron el Kia Sorento en el aeropuerto puntualmente y el vehículo estaba impecable. Totalmente recomendado.' },
            { nombre:'María Fernanda López',   lugar:'Tegucigalpa',    estrellas:5, avatar:'ML', texto:'Atención inmediata por WhatsApp, precios transparentes y el proceso fue muy fácil. El Chevrolet Colorado perfecto para nuestro viaje.' },
            { nombre:'Jorge Andrade',          lugar:'San Pedro Sula', estrellas:5, avatar:'JA', texto:'Segunda vez que los uso y siempre cumplen. El Jeep Patriot estaba en excelentes condiciones y el seguro incluido da mucha tranquilidad.' },
        ],

        // Contact
        contactBadge:    'Encuéntranos',
        contactTitle:    'Contáctanos',
        contactSubtitle: 'Visítanos en nuestra oficina o escríbenos directamente. Estamos listos para atenderte.',
        contactAddress:  'Dirección',
        contactAddr1:    'Barrio Paz Barahona',
        contactAddr2:    'Avenida Circunvalación, Plaza Nivel 1, 2do Nivel · Local #10',
        contactAddr3:    'San Pedro Sula, Cortés · Honduras',
        contactPhone:    'WhatsApp / Teléfono',
        contactPhoneNum: '+504 3248-0912',
        contactWriteNow: 'Escribir ahora →',
        contactEmail:    'Correo Electrónico',
        contactSchedule: 'Horario de Atención',
        contactMon:      'Lunes – Viernes',
        contactMonHours: '07:00 AM – 10:00 PM',
        contactSat:      'Sábados y Domingos',
        contactSatHours: 'Con cita previa',
        contactMapLabel: '📍 Nuestra Ubicación',
        contactMapName:  'Sunset RentCar · San Pedro Sula',
        contactOpenMaps: 'Abrir en Maps',

        // Gallery
        galleryBadge:    'Experiencia de viaje',
        galleryTitle:    'Vive el Camino',
        gallerySubtitle: 'Cada ruta es una experiencia. Nuestros vehículos te llevan a donde quieras ir.',
        galleryLabel1:   'Flota Premium',
        galleryText1:    'Vehículos de Alto Estándar',
        galleryLabel2:   'Aventura',
        galleryText2:    'Rutas por Honduras',
        galleryLabel3:   'Comodidad',
        galleryText3:    'Viajes en Familia',

        // Footer
        footerDesc:    'Renta de vehículos en Honduras con flota premium, seguro incluido y atención personalizada las 24 horas.',
        footerNav:     'Navegación',
        footerContact: 'Contacto',
        footerScheduleRow: 'Lun–Vie 07:00–22:00',
        footerRights:  'Todos los derechos reservados.',
        footerDev:     'Desarrollado por',

        // WhatsApp messages
        waGeneral:       '¡Hola! Quiero información sobre alquiler de vehículos.',
        waStart:         '¡Hola! Quiero comenzar mi reservación.',
        waReview:        '¡Hola! Quiero compartir mi experiencia con Sunset RentCar.',
        waWidgetDefault: 'No especificado',
        waWidgetDateDef: 'No especificada',
        waWidgetMsg:     (loc, retLoc, from, fromT, to, toT) => {
            const retLine = retLoc !== loc ? `\n📍 *Lugar de devolución:* ${retLoc}` : '';
            return `¡Hola! Quiero cotizar un vehículo 🚗\n\n📍 *Lugar de recogida:* ${loc}${retLine}\n📅 *Recogida:* ${from} a las ${fromT}\n📅 *Entrega:* ${to} a las ${toT}\n\n¿Tienen disponibilidad?`;
        },
        waTypeMsg:       (name) => `¡Hola! Me interesa rentar: ${name}. ¿Tienen disponibilidad?`,
    },

    en: {
        pageTitle: 'Sunset RentCar — Car Rental in Honduras',

        announcementBar: '✈️ Airport delivery at San Pedro Sula · 24/7 Support ·',
        announcementCta: 'Get a quote →',

        navHowItWorks: 'How it works',
        navFleet:      'Fleet',
        navServices:   'Services',
        navContact:    'Contact',
        navAdmin:      'Admin Panel',
        navWhatsapp:   'WhatsApp',

        heroBadge:    '🌅 Sunset RentCar — Honduras',
        heroTitle1:   'RENT YOUR CAR',
        heroTitle2:   'IN HONDURAS',
        heroSubtitle: 'Modern fleet · Airport delivery · 24/7 support · Insurance included',

        widgetLabel:          'Check availability now',
        widgetNote:           'No charges to quote',
        widgetLocation:       'Pickup location',
        widgetReturnLocation: 'Return location',
        widgetLocationPh:     'E.g.: SAP Airport, San Pedro Sula, Tela...',
        widgetPickupDate:     'Pickup date',
        widgetPickupTime:     'Pickup time',
        widgetReturnDate:     'Return date',
        widgetReturnTime:     'Return time',
        widgetDiffReturn:     'Return at a different location',
        widgetCta:            'Check on WhatsApp',
        trustNoHidden:     '✓ No hidden fees',
        trustImmediate:    '✓ Instant response',
        trustInsurance:    '✓ Insurance included',
        trustAirport:      '✓ Airport delivery',

        statAvail:    'Availability',
        statTypes:    'Vehicle types',
        statInsurance:'Insurance included',
        statClients:  'Happy customers',

        howBadge:    'Simple process',
        howTitle:    'How Does It Work?',
        howSubtitle: 'Rent a vehicle in 3 simple steps. No hassle, no surprises.',
        howCta:      'Start my reservation',
        steps: [
            { step:'01', icon:'🔍', title:'Choose Your Vehicle',   desc:'Browse our fleet and pick the ideal car for your trip: pickup, SUV, family or compact.' },
            { step:'02', icon:'💬', title:'Confirm via WhatsApp',   desc:'Send us your dates and pickup location. You\'ll get confirmation and price in minutes.' },
            { step:'03', icon:'🚗', title:'Receive Your Car',       desc:'We deliver the vehicle clean, insured and ready to go. At the airport or at our office.' },
        ],

        fleetBadge:    'Our fleet',
        fleetTitle:    'Available Vehicles',
        fleetSubtitle: 'Own and leased fleet, maintained to the highest standard for your comfort and safety.',
        fleetAll:      'All',
        fleetPassengers: 'passengers',
        fleetWhatsappCta:'Inquire on WhatsApp',
        vehicles: [
            { name:'Chevrolet Colorado', type:'Pickup',          emoji:'🛻', seats:5, img:'/img/ChevroletColorado-1.png',  tags:['4×4','Dual Cab','Adventure'],    wa:'Hi! I\'m interested in the Chevrolet Colorado. Is it available?' },
            { name:'Kia Sorento',        type:'3-Row SUV',       emoji:'🚐', seats:7, img:'/img/klipartz.com(1).png',       tags:['7 Seats','3 Rows','Family'],     wa:'Hi! I\'m interested in the 7-seat Kia Sorento. Is it available?' },
            { name:'Kia Sorento Sport',  type:'2-Row SUV',       emoji:'🚙', seats:5, img:'/img/KiaSorento-1.png',          tags:['5 Seats','SUV','Premium'],       wa:'Hi! I\'m interested in the Kia Sorento Sport. Is it available?' },
            { name:'Jeep Patriot',       type:'Compact',         emoji:'🚗', seats:5, img:'/img/pat4.png',                  tags:['5 Seats','Compact SUV','Economy'], wa:'Hi! I\'m interested in the Jeep Patriot. Is it available?' },
        ],

        servicesBadge:    'What\'s included',
        servicesTitle:    'Our Services',
        servicesSubtitle: 'Every rental comes with benefits that make your experience more comfortable and safe.',
        services: [
            { icon:'🧑‍✈️', title:'Driver Service',        desc:'Professional driver available for your trips.' },
            { icon:'✈️',   title:'Airport Delivery',      desc:'We meet you and deliver the vehicle at the airport.' },
            { icon:'🛟',   title:'24/7 Roadside Support', desc:'Roadside assistance available 24 hours a day.' },
            { icon:'🗺️',   title:'GPS Included',          desc:'Integrated navigation so you never get lost.' },
            { icon:'🛡️',   title:'Full Insurance',        desc:'Travel with peace of mind with full vehicle coverage.' },
            { icon:'🚿',   title:'Free Car Wash',         desc:'Vehicle delivered clean and in perfect condition.' },
        ],

        typesBadge: 'Full fleet',
        typesTitle: '6 Vehicle Types',

        testimonialsBadge:    'What our customers say',
        testimonialsTitle:    'Real Experiences',
        testimonialsSubtitle: 'Hundreds of travelers have already trusted us. Here are their experiences.',
        testimonialsLeave:    'Already rented with us? Share your experience',
        testimonialsLeaveCta: 'Leave a review →',
        testimonialsVerified: 'Verified',
        testimonials: [
            { nombre:'Carlos Mejía',           lugar:'San Pedro Sula', estrellas:5, avatar:'CM', texto:'Excellent service. They delivered the Kia Sorento at the airport on time and the vehicle was impeccable. Totally recommended.' },
            { nombre:'María Fernanda López',   lugar:'Tegucigalpa',    estrellas:5, avatar:'ML', texto:'Instant WhatsApp response, transparent prices and the process was very easy. The Chevrolet Colorado was perfect for our trip.' },
            { nombre:'Jorge Andrade',          lugar:'San Pedro Sula', estrellas:5, avatar:'JA', texto:'Second time using them and they always deliver. The Jeep Patriot was in excellent condition and the included insurance gives great peace of mind.' },
        ],

        contactBadge:    'Find us',
        contactTitle:    'Contact Us',
        contactSubtitle: 'Visit our office or write to us directly. We are ready to assist you.',
        contactAddress:  'Address',
        contactAddr1:    'Barrio Paz Barahona',
        contactAddr2:    'Avenida Circunvalación, Plaza Nivel 1, 2nd Floor · Unit #10',
        contactAddr3:    'San Pedro Sula, Cortés · Honduras',
        contactPhone:    'WhatsApp / Phone',
        contactPhoneNum: '+504 3248-0912',
        contactWriteNow: 'Write now →',
        contactEmail:    'Email',
        contactSchedule: 'Business Hours',
        contactMon:      'Monday – Friday',
        contactMonHours: '7:00 AM – 10:00 PM',
        contactSat:      'Saturdays & Sundays',
        contactSatHours: 'By appointment',
        contactMapLabel: '📍 Our Location',
        contactMapName:  'Sunset RentCar · San Pedro Sula',
        contactOpenMaps: 'Open in Maps',

        galleryBadge:    'Travel experience',
        galleryTitle:    'Live the Road',
        gallerySubtitle: 'Every route is an experience. Our vehicles take you wherever you want to go.',
        galleryLabel1:   'Premium Fleet',
        galleryText1:    'High Standard Vehicles',
        galleryLabel2:   'Adventure',
        galleryText2:    'Routes Through Honduras',
        galleryLabel3:   'Comfort',
        galleryText3:    'Family Trips',

        footerDesc:    'Vehicle rentals in Honduras with a premium fleet, insurance included and personalized 24-hour service.',
        footerNav:     'Navigation',
        footerContact: 'Contact',
        footerScheduleRow: 'Mon–Fri 7:00 AM–10:00 PM',
        footerRights:  'All rights reserved.',
        footerDev:     'Developed by',

        waGeneral:       'Hi! I want information about car rentals.',
        waStart:         'Hi! I want to start my reservation.',
        waReview:        'Hi! I want to share my experience with Sunset RentCar.',
        waWidgetDefault: 'Not specified',
        waWidgetDateDef: 'Not specified',
        waWidgetMsg:     (loc, retLoc, from, fromT, to, toT) => {
            const retLine = retLoc !== loc ? `\n📍 *Return location:* ${retLoc}` : '';
            return `Hi! I'd like to quote a vehicle 🚗\n\n📍 *Pickup location:* ${loc}${retLine}\n📅 *Pickup:* ${from} at ${fromT}\n📅 *Return:* ${to} at ${toT}\n\nDo you have availability?`;
        },
        waTypeMsg:       (name) => `Hi! I'm interested in renting: ${name}. Do you have availability?`,
    },
};

const tx = computed(() => i18n[locale.value]);

// ── WhatsApp ─────────────────────────────────────────────────────────────────
const whatsappNumber = '50432480912';
const getWhatsappLink = (msg = '') => {
    const text = msg || tx.value.waGeneral;
    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(text)}`;
};

// ── Navbar scroll + mobile menu ──────────────────────────────────────────────
const scrolled   = ref(false);
const mobileOpen = ref(false);

// ── 3D Particle Canvas ───────────────────────────────────────────────────────
let animFrameId = null;
let particles   = [];
let mouse       = { x: 0, y: 0 };

function initParticleCanvas() {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const resize = () => {
        canvas.width  = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    // Create particles
    const COUNT = 90;
    particles = [];
    for (let i = 0; i < COUNT; i++) {
        particles.push({
            x:    Math.random() * canvas.width,
            y:    Math.random() * canvas.height,
            r:    Math.random() * 1.8 + 0.3,
            vx:   (Math.random() - 0.5) * 0.35,
            vy:   (Math.random() - 0.5) * 0.35,
            alpha: Math.random() * 0.5 + 0.1,
            hue:  Math.random() * 40 + 20, // amber/orange range
        });
    }

    // Speed lines
    const LINES = 18;
    const lines = [];
    for (let i = 0; i < LINES; i++) {
        lines.push({
            x:     Math.random() * canvas.width,
            y:     Math.random() * canvas.height,
            len:   Math.random() * 120 + 40,
            speed: Math.random() * 3 + 1.5,
            alpha: Math.random() * 0.12 + 0.03,
        });
    }

    const draw = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Mouse-reactive glow
        const gx = (mouse.x / window.innerWidth)  * canvas.width;
        const gy = (mouse.y / window.innerHeight) * canvas.height;
        const grad = ctx.createRadialGradient(gx, gy, 0, gx, gy, 320);
        grad.addColorStop(0, 'rgba(251,146,60,0.07)');
        grad.addColorStop(1, 'rgba(0,0,0,0)');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Speed lines
        lines.forEach(l => {
            ctx.save();
            ctx.strokeStyle = `rgba(251,191,36,${l.alpha})`;
            ctx.lineWidth   = 1;
            ctx.beginPath();
            ctx.moveTo(l.x, l.y);
            ctx.lineTo(l.x - l.len, l.y);
            ctx.stroke();
            ctx.restore();
            l.x += l.speed;
            if (l.x > canvas.width + l.len) l.x = -l.len;
        });

        // Particles
        particles.forEach(p => {
            ctx.save();
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fillStyle = `hsla(${p.hue}, 90%, 65%, ${p.alpha})`;
            ctx.shadowColor = `hsla(${p.hue}, 90%, 65%, 0.6)`;
            ctx.shadowBlur  = 6;
            ctx.fill();
            ctx.restore();

            // Mouse attraction (subtle)
            const dx = gx - p.x;
            const dy = gy - p.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 200) {
                p.vx += dx / dist * 0.012;
                p.vy += dy / dist * 0.012;
            }

            p.vx *= 0.985;
            p.vy *= 0.985;
            p.x  += p.vx;
            p.y  += p.vy;
            if (p.x < 0) p.x = canvas.width;
            if (p.x > canvas.width)  p.x = 0;
            if (p.y < 0) p.y = canvas.height;
            if (p.y > canvas.height) p.y = 0;
        });

        // Particle connections
        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
                const dx = particles[i].x - particles[j].x;
                const dy = particles[i].y - particles[j].y;
                const d  = Math.sqrt(dx * dx + dy * dy);
                if (d < 90) {
                    ctx.save();
                    ctx.strokeStyle = `rgba(251,191,36,${0.06 * (1 - d / 90)})`;
                    ctx.lineWidth   = 0.5;
                    ctx.beginPath();
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        }

        animFrameId = requestAnimationFrame(draw);
    };
    draw();
}

// ── 3D Tilt on vehicle cards ──────────────────────────────────────────────────
function initCardTilt() {
    const cards = document.querySelectorAll('.tilt-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect   = card.getBoundingClientRect();
            const cx     = rect.left + rect.width  / 2;
            const cy     = rect.top  + rect.height / 2;
            const dx     = (e.clientX - cx) / (rect.width  / 2);
            const dy     = (e.clientY - cy) / (rect.height / 2);
            const rotX   = -dy * 10;
            const rotY   =  dx * 10;
            card.style.transform = `perspective(700px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateZ(8px)`;
            const img = card.querySelector('img');
            if (img) img.style.transform = `translateX(${dx * 8}px) translateY(${dy * 4}px) scale(1.06)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
            const img = card.querySelector('img');
            if (img) img.style.transform = '';
        });
    });
}

// ── Animated counters ─────────────────────────────────────────────────────────
function animateCounter(el, target, suffix = '', duration = 1800) {
    let start = null;
    const step = (ts) => {
        if (!start) start = ts;
        const progress = Math.min((ts - start) / duration, 1);
        const eased    = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target) + suffix;
        if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

function initCounters() {
    const counters = document.querySelectorAll('[data-count]');
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el     = entry.target;
                const target = parseFloat(el.dataset.count);
                const suffix = el.dataset.suffix || '';
                animateCounter(el, target, suffix);
                obs.unobserve(el);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => obs.observe(c));
}

onMounted(() => {
    AOS.init({ duration: 800, once: true, easing: 'ease-out-cubic', offset: 60 });

    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    });

    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 60;
        if (window.scrollY > 60) mobileOpen.value = false;
    });

    // Slight delay so DOM is ready
    setTimeout(() => {
        initParticleCanvas();
        initCardTilt();
        initCounters();
    }, 100);
});

onUnmounted(() => {
    if (animFrameId) cancelAnimationFrame(animFrameId);
});

// ── Widget de búsqueda ───────────────────────────────────────────────────────
const search = ref({
    location:        '',
    differentReturn: false,
    returnLocation:  '',
    pickupDate:      '',
    pickupTime:      '08:00',
    returnDate:      '',
    returnTime:      '08:00',
});
const focusedField = ref(null);
const today = new Date().toISOString().split('T')[0];

const timeSlots = Array.from({ length: 34 }, (_, i) => {
    const h  = Math.floor(i / 2) + 7;
    const m  = i % 2 === 0 ? '00' : '30';
    const hh = String(h).padStart(2, '0');
    return `${hh}:${m}`;
});

const searchWhatsapp = () => {
    const t     = tx.value;
    const loc   = search.value.location       || t.waWidgetDefault;
    const retLoc = search.value.differentReturn
        ? (search.value.returnLocation || t.waWidgetDefault)
        : loc;
    const from  = search.value.pickupDate     || t.waWidgetDateDef;
    const fromT = search.value.pickupTime;
    const to    = search.value.returnDate     || t.waWidgetDateDef;
    const toT   = search.value.returnTime;
    window.open(getWhatsappLink(t.waWidgetMsg(loc, retLoc, from, fromT, to, toT)), '_blank');
};

// ── Catálogo: filtro ─────────────────────────────────────────────────────────
const activeType = ref(null);
const filtered = computed(() => {
    const vlist = tx.value.vehicles;
    return activeType.value ? vlist.filter(v => v.type === activeType.value) : vlist;
});
const uniqueTypes = computed(() => [...new Set(tx.value.vehicles.map(v => v.type))]);
</script>

<template>
    <Head :title="tx.pageTitle" />

    <!-- ═══════════════════ ANNOUNCEMENT BAR ════════════════════════════════ -->
    <div class="bg-gradient-to-r from-amber-600 via-yellow-400 to-orange-500 text-black text-xs font-bold py-2 px-4 text-center tracking-wide">
        {{ tx.announcementBar }}
        <a :href="getWhatsappLink(tx.waGeneral)" target="_blank"
           class="underline hover:opacity-70 transition-opacity ml-1">
            {{ tx.announcementCta }}
        </a>
    </div>

    <!-- ═══════════════════ NAVBAR ══════════════════════════════════════════ -->
    <header class="sticky top-0 left-0 right-0 z-50 transition-all duration-500"
        :class="scrolled
            ? 'bg-[#0a0806]/96 backdrop-blur-md shadow-lg shadow-black/60'
            : 'bg-[#0a0806]/75 backdrop-blur-sm'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between py-3">

            <!-- Logo -->
            <a href="#inicio" @click="mobileOpen = false">
                <img src="/img/logo.png" alt="Sunset RentCar" class="h-12 w-auto"
                    style="filter: drop-shadow(0 0 14px rgba(251,191,36,0.8)) drop-shadow(0 2px 4px rgba(0,0,0,0.9)) brightness(1.15)"/>
            </a>

            <!-- Nav desktop -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium">
                <a href="#como-funciona" class="text-amber-100/70 hover:text-amber-300 transition-colors">{{ tx.navHowItWorks }}</a>
                <a href="#flota"         class="text-amber-100/70 hover:text-amber-300 transition-colors">{{ tx.navFleet }}</a>
                <a href="#servicios"     class="text-amber-100/70 hover:text-amber-300 transition-colors">{{ tx.navServices }}</a>
                <a href="#contacto"      class="text-amber-100/70 hover:text-amber-300 transition-colors">{{ tx.navContact }}</a>
                <Link :href="route('login')"
                    class="px-3 py-1.5 border border-amber-400/30 hover:border-amber-400 text-amber-100/60 hover:text-amber-300 rounded-lg transition-all text-sm">
                    {{ tx.navAdmin }}
                </Link>

                <!-- Language toggle -->
                <button @click="toggleLocale"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-amber-400/30
                           hover:border-amber-400/60 text-amber-100/60 hover:text-amber-300 transition-all text-sm font-bold">
                    <span class="text-base">{{ locale === 'es' ? '🇭🇳' : '🇺🇸' }}</span>
                    {{ locale === 'es' ? 'EN' : 'ES' }}
                </button>

                <a :href="getWhatsappLink()" target="_blank"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-400 text-white font-bold rounded-lg transition-all hover:scale-105 shadow-lg shadow-green-900/40">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    {{ tx.navWhatsapp }}
                </a>
            </nav>

            <!-- Mobile: lang + WA + hamburger -->
            <div class="flex md:hidden items-center gap-2">
                <button @click="toggleLocale"
                    class="w-9 h-9 rounded-lg border border-amber-400/30 flex items-center justify-center text-amber-100/70 text-xs font-bold">
                    {{ locale === 'es' ? 'EN' : 'ES' }}
                </button>
                <a :href="getWhatsappLink()" target="_blank"
                    class="flex items-center gap-1.5 px-3 py-2 bg-green-500 text-white text-sm font-bold rounded-lg">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    WA
                </a>
                <button @click="mobileOpen = !mobileOpen"
                    class="w-9 h-9 flex flex-col items-center justify-center gap-1.5 rounded-lg border border-amber-400/20 hover:border-amber-400/50 transition-colors">
                    <span class="block w-5 h-0.5 bg-amber-200/70 transition-all duration-300" :class="mobileOpen ? 'rotate-45 translate-y-2' : ''"></span>
                    <span class="block w-5 h-0.5 bg-amber-200/70 transition-all duration-300" :class="mobileOpen ? 'opacity-0' : ''"></span>
                    <span class="block w-5 h-0.5 bg-amber-200/70 transition-all duration-300" :class="mobileOpen ? '-rotate-45 -translate-y-2' : ''"></span>
                </button>
            </div>
        </div>

        <!-- Mobile drawer -->
        <div class="md:hidden overflow-hidden transition-all duration-300 border-t border-amber-400/10"
             :class="mobileOpen ? 'max-h-80 opacity-100' : 'max-h-0 opacity-0'">
            <nav class="px-4 py-4 bg-[#0a0806]/98 backdrop-blur-md flex flex-col gap-1">
                <a href="#como-funciona" @click="mobileOpen=false" class="px-3 py-3 text-amber-100/70 hover:text-amber-300 hover:bg-amber-400/5 rounded-lg text-sm font-medium transition-all">{{ tx.navHowItWorks }}</a>
                <a href="#flota"         @click="mobileOpen=false" class="px-3 py-3 text-amber-100/70 hover:text-amber-300 hover:bg-amber-400/5 rounded-lg text-sm font-medium transition-all">{{ tx.navFleet }}</a>
                <a href="#servicios"     @click="mobileOpen=false" class="px-3 py-3 text-amber-100/70 hover:text-amber-300 hover:bg-amber-400/5 rounded-lg text-sm font-medium transition-all">{{ tx.navServices }}</a>
                <a href="#contacto"      @click="mobileOpen=false" class="px-3 py-3 text-amber-100/70 hover:text-amber-300 hover:bg-amber-400/5 rounded-lg text-sm font-medium transition-all">{{ tx.navContact }}</a>
                <Link :href="route('login')" @click="mobileOpen=false" class="px-3 py-3 text-amber-100/70 hover:text-amber-300 hover:bg-amber-400/5 rounded-lg text-sm font-medium transition-all">{{ tx.navAdmin }}</Link>
            </nav>
        </div>
    </header>

    <!-- ═══════════════════ HERO ═════════════════════════════════════════════ -->
    <section id="inicio" class="relative min-h-screen bg-[#080604] overflow-hidden flex flex-col justify-center">

        <!-- Video de fondo -->
        <video autoplay muted loop playsinline
            class="absolute inset-0 w-full h-full object-cover pointer-events-none"
            style="opacity:0.45; z-index:0;">
            <source src="/img/Video_Para_Landing_Page_Veh%C3%ADculos.mp4" type="video/mp4">
            <source src="/img/12127397_3840_2160_30fps.mp4" type="video/mp4">
        </video>

        <!-- Canvas 3D: partículas + speed lines -->
        <canvas id="hero-canvas"
            class="absolute inset-0 w-full h-full pointer-events-none"
            style="z-index:2;"></canvas>

        <!-- Gradiente sunset en el fondo -->
        <div class="absolute inset-0 pointer-events-none"
             style="z-index:1; background: radial-gradient(ellipse at 50% 110%, rgba(251,146,60,0.25) 0%, rgba(234,179,8,0.12) 35%, transparent 65%);"></div>
        <!-- Overlay oscuro direccional -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#080604]/80 via-[#080604]/30 to-[#080604]/90 pointer-events-none" style="z-index:1;"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#080604]/65 via-transparent to-[#080604]/65 pointer-events-none" style="z-index:1;"></div>

        <!-- Animated sunset glow rings -->
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 pointer-events-none" style="z-index:1;">
            <div class="glow-ring" style="--s:700px;--c:rgba(251,146,60,0.07);--d:0s;"></div>
            <div class="glow-ring" style="--s:900px;--c:rgba(234,179,8,0.05);--d:1.2s;"></div>
            <div class="glow-ring" style="--s:1100px;--c:rgba(251,146,60,0.03);--d:2.4s;"></div>
        </div>

        <!-- Contenido centrado -->
        <div class="relative w-full max-w-6xl mx-auto px-4 sm:px-6 pt-28 pb-20 flex flex-col items-center text-center" style="z-index:3;">

            <!-- Badge con pulse flotante -->
            <div class="inline-flex items-center gap-2 bg-amber-400/10 border border-amber-400/35
                        text-amber-300 text-xs font-bold px-5 py-2 rounded-full mb-8 uppercase tracking-widest backdrop-blur-sm badge-float"
                data-aos="fade-down">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping inline-block"></span>
                {{ tx.heroBadge }}
            </div>

            <!-- Titular 3D + shimmer -->
            <h1 class="text-5xl sm:text-6xl lg:text-8xl font-black text-white leading-none mb-5 tracking-tight hero-title-3d"
                data-aos="fade-up" data-aos-delay="80">
                <span class="block">{{ tx.heroTitle1 }}</span>
                <span class="shimmer-text">{{ tx.heroTitle2 }}</span>
            </h1>

            <p class="text-amber-100/60 text-lg sm:text-xl mb-12 max-w-xl leading-relaxed"
                data-aos="fade-up" data-aos-delay="160">
                {{ tx.heroSubtitle }}
            </p>

            <!-- ══ WIDGET ESTILO SIXT ══════════════════════════════════════ -->
            <div class="w-full max-w-5xl" data-aos="fade-up" data-aos-delay="260">

                <!-- Toggle diferente lugar de devolución -->
                <div class="flex items-center gap-3 mb-3 justify-start pl-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none group">
                        <span class="relative inline-flex h-5 w-9 shrink-0">
                            <input v-model="search.differentReturn" type="checkbox" class="peer sr-only"/>
                            <span class="absolute inset-0 rounded-full bg-white/20 peer-checked:bg-amber-400 transition-colors duration-200"></span>
                            <span class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow transition-transform duration-200 peer-checked:translate-x-4"></span>
                        </span>
                        <span class="text-amber-100/60 text-xs font-medium group-hover:text-amber-200 transition-colors">
                            {{ tx.widgetDiffReturn }}
                        </span>
                    </label>
                </div>

                <!-- Widget 2 filas -->
                <div class="rounded-2xl overflow-hidden shadow-2xl shadow-black/70"
                     style="background:#fffdf8; border:1px solid rgba(251,191,36,0.15);">

                    <!-- FILA 1: Ubicación -->
                    <div class="flex flex-col sm:flex-row border-b" style="border-color:#e8e4dc;">

                        <!-- Lugar de recogida -->
                        <div class="flex-1 flex flex-col px-6 pt-5 pb-4 transition-colors border-b sm:border-b-0 sm:border-r"
                             :style="{borderColor:'#e8e4dc', background: focusedField==='location' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2" style="color:#f59e0b;">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                {{ tx.widgetLocation }}
                            </span>
                            <input v-model="search.location" type="text"
                                :placeholder="tx.widgetLocationPh"
                                @focus="focusedField='location'" @blur="focusedField=null"
                                class="w-full bg-transparent text-base font-semibold outline-none"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;"/>
                        </div>

                        <!-- Lugar devolución (opcional) -->
                        <div v-if="search.differentReturn"
                             class="flex-1 flex flex-col px-6 pt-5 pb-4 transition-colors"
                             :style="{background: focusedField==='returnLocation' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2" style="color:#f97316;">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                {{ tx.widgetReturnLocation }}
                            </span>
                            <input v-model="search.returnLocation" type="text"
                                :placeholder="tx.widgetLocationPh"
                                @focus="focusedField='returnLocation'" @blur="focusedField=null"
                                class="w-full bg-transparent text-base font-semibold outline-none"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;"/>
                        </div>
                    </div>

                    <!-- FILA 2: Fechas + Horas + Botón -->
                    <div class="flex flex-col sm:flex-row sm:items-stretch">

                        <!-- Fecha recogida -->
                        <div class="flex-1 flex flex-col px-5 pt-4 pb-4 transition-colors border-b sm:border-b-0 sm:border-r"
                             :style="{borderColor:'#e8e4dc', background: focusedField==='pickupDate' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2 whitespace-nowrap" style="color:#f59e0b;">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ tx.widgetPickupDate }}
                            </span>
                            <input v-model="search.pickupDate" type="date" :min="today"
                                @focus="focusedField='pickupDate'" @blur="focusedField=null"
                                class="w-full bg-transparent text-sm font-semibold outline-none cursor-pointer [color-scheme:light]"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;"/>
                        </div>

                        <!-- Hora recogida -->
                        <div class="flex-1 flex flex-col px-5 pt-4 pb-4 transition-colors border-b sm:border-b-0 sm:border-r"
                             :style="{borderColor:'#e8e4dc', background: focusedField==='pickupTime' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2 whitespace-nowrap" style="color:#f59e0b;">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ tx.widgetPickupTime }}
                            </span>
                            <select v-model="search.pickupTime"
                                @focus="focusedField='pickupTime'" @blur="focusedField=null"
                                class="w-full bg-transparent text-sm font-semibold outline-none cursor-pointer"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;">
                                <option v-for="t in timeSlots" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>

                        <!-- Fecha entrega -->
                        <div class="flex-1 flex flex-col px-5 pt-4 pb-4 transition-colors border-b sm:border-b-0 sm:border-r"
                             :style="{borderColor:'#e8e4dc', background: focusedField==='returnDate' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2 whitespace-nowrap" style="color:#f97316;">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ tx.widgetReturnDate }}
                            </span>
                            <input v-model="search.returnDate" type="date" :min="search.pickupDate || today"
                                @focus="focusedField='returnDate'" @blur="focusedField=null"
                                class="w-full bg-transparent text-sm font-semibold outline-none cursor-pointer [color-scheme:light]"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;"/>
                        </div>

                        <!-- Hora entrega -->
                        <div class="flex-1 flex flex-col px-5 pt-4 pb-4 transition-colors border-b sm:border-b-0 sm:border-r"
                             :style="{borderColor:'#e8e4dc', background: focusedField==='returnTime' ? '#fff5e6' : 'transparent'}">
                            <span class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest mb-2 whitespace-nowrap" style="color:#f97316;">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ tx.widgetReturnTime }}
                            </span>
                            <select v-model="search.returnTime"
                                @focus="focusedField='returnTime'" @blur="focusedField=null"
                                class="w-full bg-transparent text-sm font-semibold outline-none cursor-pointer"
                                style="color:#1c1a16; font-family:'Inter',sans-serif;">
                                <option v-for="t in timeSlots" :key="t" :value="t">{{ t }}</option>
                            </select>
                        </div>

                        <!-- Botón CTA -->
                        <button @click="searchWhatsapp"
                            class="flex items-center justify-center gap-2 px-7 py-5 font-black text-sm text-black transition-all active:scale-95 hover:brightness-110 whitespace-nowrap sm:min-w-[185px]"
                            style="background:linear-gradient(135deg,#fbbf24,#f97316); font-family:'Syne',sans-serif;">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                            <span>{{ tx.widgetCta }}</span>
                        </button>
                     </div>
                </div><!-- /widget card -->
            </div><!-- /max-w-5xl -->

                <!-- Trust signals bajo el widget -->
                <div class="mt-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-1">
                    <span class="text-xs text-amber-100/40 flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-green-400 inline-block"></span>{{ tx.trustNoHidden }}
                    </span>
                    <span class="text-xs text-amber-100/40 flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-green-400 inline-block"></span>{{ tx.trustImmediate }}
                    </span>
                    <span class="text-xs text-amber-100/40 flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-green-400 inline-block"></span>{{ tx.trustInsurance }}
                    </span>
                    <span class="text-xs text-amber-100/40 flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-green-400 inline-block"></span>{{ tx.trustAirport }}
                    </span>
                </div>

            <!-- Scroll indicator -->
            <div class="mt-14 flex flex-col items-center gap-2 text-amber-100/20 text-xs">
                <div class="w-px h-10 bg-gradient-to-b from-transparent to-amber-400/40 animate-pulse"></div>
                <span class="tracking-widest uppercase text-[10px]">scroll</span>
            </div>
        </div><!-- /contenido centrado -->
    </section>

    <!-- ═══════════════════ STATS BAR ════════════════════════════════════════ -->
    <section class="relative py-10 overflow-hidden" style="background:linear-gradient(135deg,#f59e0b,#f97316,#fbbf24);">
        <!-- 3D animated shimmer overlay -->
        <div class="absolute inset-0 pointer-events-none stats-shine"></div>
        <div class="max-w-4xl mx-auto px-6 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center relative">
            <div data-aos="zoom-in" data-aos-delay="0" class="stat-card-3d">
                <div class="text-4xl font-black text-black" data-count="24" data-suffix="/7">24/7</div>
                <div class="text-sm text-amber-900 font-medium mt-1">{{ tx.statAvail }}</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="80" class="stat-card-3d">
                <div class="text-4xl font-black text-black" data-count="6" data-suffix="+">6+</div>
                <div class="text-sm text-amber-900 font-medium mt-1">{{ tx.statTypes }}</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="160" class="stat-card-3d">
                <div class="text-4xl font-black text-black" data-count="100" data-suffix="%">100%</div>
                <div class="text-sm text-amber-900 font-medium mt-1">{{ tx.statInsurance }}</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="240" class="stat-card-3d">
                <div class="text-4xl font-black text-black">⭐</div>
                <div class="text-sm text-amber-900 font-medium mt-1">{{ tx.statClients }}</div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CÓMO FUNCIONA ════════════════════════════════════ -->
    <section id="como-funciona" class="py-24 relative overflow-hidden" style="background:#0c0a07;">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-amber-500/30 to-transparent"></div>
        <div class="absolute inset-0 pointer-events-none"
             style="background: radial-gradient(ellipse at 50% 0%, rgba(251,146,60,0.05) 0%, transparent 60%);"></div>

        <div class="max-w-7xl mx-auto px-6 relative">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.howBadge }}</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.howTitle }}</h2>
                <p class="text-amber-100/40 mt-4 max-w-lg mx-auto">{{ tx.howSubtitle }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Línea conectora desktop -->
                <div class="hidden md:block absolute top-16 h-px bg-gradient-to-r from-amber-500/20 via-amber-400/50 to-amber-500/20 pointer-events-none"
                     style="left:16.66%;right:16.66%"></div>

                <div v-for="(paso, i) in tx.steps" :key="paso.step"
                    data-aos="fade-up" :data-aos-delay="i * 120"
                    class="relative flex flex-col items-center text-center group">
                    <div class="relative mb-6">
                        <div class="absolute inset-0 rounded-full bg-amber-400/8 scale-125 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="relative w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 via-yellow-400 to-orange-500 flex items-center justify-center shadow-xl shadow-amber-400/20 group-hover:shadow-amber-400/40 transition-shadow">
                            <span class="text-3xl">{{ paso.icon }}</span>
                        </div>
                        <div class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-[#0c0a07] border-2 border-amber-400 flex items-center justify-center">
                            <span class="text-amber-400 text-xs font-black">{{ paso.step }}</span>
                        </div>
                    </div>
                    <h3 class="text-white font-bold text-xl mb-3">{{ paso.title }}</h3>
                    <p class="text-amber-100/40 text-sm leading-relaxed max-w-xs">{{ paso.desc }}</p>
                </div>
            </div>

            <div class="text-center mt-14" data-aos="fade-up">
                <a :href="getWhatsappLink(tx.waStart)" target="_blank"
                    class="inline-flex items-center gap-3 px-8 py-4
                           bg-gradient-to-r from-amber-400 via-yellow-400 to-orange-400
                           hover:from-amber-300 hover:to-orange-300
                           text-black font-black text-base rounded-xl transition-all hover:scale-105 hover:shadow-xl hover:shadow-amber-400/25">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    {{ tx.howCta }}
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CATÁLOGO ═════════════════════════════════════════ -->
    <section id="flota" class="py-24" style="background:#0e0b08;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-14" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.fleetBadge }}</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.fleetTitle }}</h2>
                <p class="text-amber-100/40 mt-4 max-w-lg mx-auto">{{ tx.fleetSubtitle }}</p>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-2 justify-center mb-10" data-aos="fade-up">
                <button @click="activeType = null"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-all"
                    :class="!activeType ? 'bg-amber-400 text-black border-amber-400' : 'border-amber-400/20 text-amber-100/50 hover:border-amber-400/50 hover:text-amber-300'">
                    {{ tx.fleetAll }}
                </button>
                <button v-for="t in uniqueTypes" :key="t" @click="activeType = t"
                    class="px-4 py-2 rounded-full text-sm font-semibold border transition-all"
                    :class="activeType === t ? 'bg-amber-400 text-black border-amber-400' : 'border-amber-400/20 text-amber-100/50 hover:border-amber-400/50 hover:text-amber-300'">
                    {{ t }}
                </button>
            </div>

            <!-- Cards 3D -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="(v, i) in filtered" :key="v.name"
                    data-aos="fade-up" :data-aos-delay="i * 80"
                    class="tilt-card group rounded-2xl overflow-hidden border border-amber-400/10 hover:border-amber-400/40 transition-all hover:shadow-2xl hover:shadow-amber-400/15"
                    style="background:#171108; transition: transform 0.15s ease, box-shadow 0.3s ease;">

                    <!-- Imagen -->
                    <div class="relative pt-8 px-4 pb-2 flex items-end justify-center h-48 overflow-hidden"
                         style="background: linear-gradient(to bottom, #1f1610, #171108);">
                        <div class="absolute inset-0" style="background: radial-gradient(ellipse at bottom, rgba(251,191,36,0.07) 0%, transparent 70%);"></div>
                        <img :src="v.img" :alt="v.name"
                            class="h-36 w-auto object-contain relative z-10 drop-shadow-2xl group-hover:scale-105 transition-transform duration-500 brightness-105"/>
                        <div class="absolute top-3 left-3 bg-gradient-to-r from-amber-400 to-orange-400 text-black text-xs font-black px-2 py-1 rounded-full">
                            {{ v.emoji }} {{ v.type }}
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-5">
                        <h3 class="text-white font-bold text-xl">{{ v.name }}</h3>
                        <p class="text-amber-100/40 text-sm mt-1">👥 {{ v.seats }} {{ tx.fleetPassengers }}</p>
                        <div class="flex flex-wrap gap-1 mt-3">
                            <span v-for="tag in v.tags" :key="tag"
                                class="text-xs px-2 py-1 rounded-full border text-amber-100/40 border-amber-400/15"
                                style="background:#1f1610;">
                                {{ tag }}
                            </span>
                        </div>
                        <a :href="getWhatsappLink(v.wa)" target="_blank"
                            class="mt-5 w-full flex items-center justify-center gap-2 py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition-all text-sm group-hover:shadow-lg group-hover:shadow-green-900/40">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                            {{ tx.fleetWhatsappCta }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ SERVICIOS ════════════════════════════════════════ -->
    <section id="servicios" class="py-24 relative overflow-hidden" style="background:#090705;">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
        <div class="absolute inset-0 pointer-events-none"
             style="background: radial-gradient(ellipse at 80% 50%, rgba(251,146,60,0.04) 0%, transparent 60%);"></div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.servicesBadge }}</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.servicesTitle }}</h2>
                <p class="text-amber-100/40 mt-4 max-w-lg mx-auto">{{ tx.servicesSubtitle }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div v-for="(s, i) in tx.services" :key="s.title"
                    data-aos="fade-up" :data-aos-delay="(i % 3) * 100"
                    class="flex items-start gap-4 p-6 rounded-2xl border border-amber-400/10 hover:border-amber-400/30 transition-all group"
                    style="background:#0f0c08;">
                    <div class="text-4xl shrink-0 group-hover:scale-110 transition-transform">{{ s.icon }}</div>
                    <div>
                        <h3 class="text-white font-bold text-base mb-1">{{ s.title }}</h3>
                        <p class="text-amber-100/40 text-sm leading-relaxed">{{ s.desc }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ TIPOS DE VEHÍCULO ════════════════════════════════ -->
    <section class="py-20" style="background:#0c0a07;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.typesBadge }}</span>
                <h2 class="text-4xl font-black text-white mt-3">{{ tx.typesTitle }}</h2>
            </div>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
                <a v-for="(type, i) in vehicleTypes" :key="type.id"
                    :href="getWhatsappLink(tx.waTypeMsg(type.name))"
                    target="_blank"
                    data-aos="zoom-in" :data-aos-delay="i * 60"
                    class="group flex flex-col items-center gap-2 p-4 rounded-xl border border-amber-400/10
                           hover:border-amber-400/40 hover:bg-amber-400/4 transition-all text-center cursor-pointer">
                    <span class="text-4xl group-hover:scale-110 transition-transform">{{ type.emoji }}</span>
                    <span class="text-xs font-semibold text-amber-100/40 group-hover:text-amber-300 transition-colors leading-tight">{{ type.name }}</span>
                    <span class="text-xs text-amber-100/20">{{ type.seats }}p</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ TESTIMONIOS ══════════════════════════════════════ -->
    <section class="py-24 relative overflow-hidden" style="background:#090705;">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-amber-400/25 to-transparent"></div>
        <div class="absolute inset-0 pointer-events-none"
             style="background: radial-gradient(ellipse at 20% 50%, rgba(234,179,8,0.03) 0%, transparent 60%);"></div>

        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.testimonialsBadge }}</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.testimonialsTitle }}</h2>
                <p class="text-amber-100/40 mt-4 max-w-lg mx-auto">{{ tx.testimonialsSubtitle }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="(t, i) in tx.testimonials" :key="t.nombre"
                    data-aos="fade-up" :data-aos-delay="i * 100"
                    class="relative rounded-2xl p-6 border border-amber-400/10 hover:border-amber-400/25 transition-all group hover:-translate-y-1"
                    style="background:#0f0c08;">
                    <!-- Comilla decorativa -->
                    <div class="absolute top-5 right-6 text-6xl leading-none font-black select-none text-amber-400/8 group-hover:text-amber-400/15 transition-colors">"</div>
                    <!-- Estrellas -->
                    <div class="flex gap-1 mb-4">
                        <span v-for="n in t.estrellas" :key="n" class="text-amber-400 text-lg">★</span>
                    </div>
                    <p class="text-amber-100/50 text-sm leading-relaxed mb-6 relative z-10">"{{ t.texto }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shrink-0">
                            <span class="text-black text-sm font-black">{{ t.avatar }}</span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ t.nombre }}</p>
                            <p class="text-amber-100/30 text-xs">📍 {{ t.lugar }}</p>
                        </div>
                        <div class="ml-auto">
                            <span class="text-xs text-green-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ tx.testimonialsVerified }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12" data-aos="fade-up">
                <p class="text-amber-100/30 text-sm mb-4">{{ tx.testimonialsLeave }}</p>
                <a :href="getWhatsappLink(tx.waReview)" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 border border-amber-400/25 text-amber-400 hover:bg-amber-400/8 rounded-xl transition-all text-sm font-semibold">
                    {{ tx.testimonialsLeaveCta }}
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ CONTACTO + MAPA ══════════════════════════════════ -->
    <section id="contacto" class="relative overflow-hidden" style="background:#0c0a07;">
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-amber-400/30 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-6 pt-20 pb-12 text-center" data-aos="fade-up">
            <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.contactBadge }}</span>
            <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.contactTitle }}</h2>
            <p class="text-amber-100/40 mt-3 max-w-lg mx-auto">{{ tx.contactSubtitle }}</p>
        </div>

        <div class="max-w-7xl mx-auto px-6 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

                <!-- Panel info -->
                <div class="flex flex-col gap-4" data-aos="fade-right">

                    <!-- Dirección -->
                    <div class="border border-amber-400/10 hover:border-amber-400/25 rounded-2xl p-6 transition-colors group" style="background:#131009;">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-amber-400/8 border border-amber-400/15 flex items-center justify-center shrink-0 group-hover:bg-amber-400/15 transition-colors">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-1">{{ tx.contactAddress }}</p>
                                <p class="text-white font-semibold leading-snug">{{ tx.contactAddr1 }}</p>
                                <p class="text-amber-100/50 text-sm mt-0.5">{{ tx.contactAddr2 }}</p>
                                <p class="text-amber-100/35 text-sm">{{ tx.contactAddr3 }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="border border-amber-400/10 hover:border-amber-400/25 rounded-2xl p-6 transition-colors group" style="background:#131009;">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-green-500/8 border border-green-500/15 flex items-center justify-center shrink-0 group-hover:bg-green-500/15 transition-colors">
                                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-green-400 text-xs font-bold uppercase tracking-widest mb-1">{{ tx.contactPhone }}</p>
                                <p class="text-white font-semibold">{{ tx.contactPhoneNum }}</p>
                                <a :href="getWhatsappLink(tx.waGeneral)" target="_blank"
                                    class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-bold rounded-xl transition-all hover:scale-105">
                                    {{ tx.contactWriteNow }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="border border-amber-400/10 hover:border-amber-400/25 rounded-2xl p-6 transition-colors group" style="background:#131009;">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-amber-400/8 border border-amber-400/15 flex items-center justify-center shrink-0 group-hover:bg-amber-400/15 transition-colors">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-1">{{ tx.contactEmail }}</p>
                                <a href="mailto:info@sunsetrentcar.com" class="text-white font-semibold hover:text-amber-400 transition-colors">
                                    info@sunsetrentcar.com
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Horario -->
                    <div class="border border-amber-400/10 hover:border-amber-400/25 rounded-2xl p-6 transition-colors group" style="background:#131009;">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-amber-400/8 border border-amber-400/15 flex items-center justify-center shrink-0 group-hover:bg-amber-400/15 transition-colors">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-amber-400 text-xs font-bold uppercase tracking-widest mb-2">{{ tx.contactSchedule }}</p>
                                <div class="space-y-1 text-sm">
                                    <div class="flex justify-between gap-8">
                                        <span class="text-amber-100/40">{{ tx.contactMon }}</span>
                                        <span class="text-white font-medium">{{ tx.contactMonHours }}</span>
                                    </div>
                                    <div class="flex justify-between gap-8">
                                        <span class="text-amber-100/40">{{ tx.contactSat }}</span>
                                        <span class="text-white font-medium">{{ tx.contactSatHours }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="rounded-2xl overflow-hidden border border-amber-400/10 min-h-[420px] lg:min-h-0 relative" data-aos="fade-left">
                    <div class="absolute top-0 left-0 right-0 z-10 bg-gradient-to-b from-black/80 to-transparent px-5 py-4 pointer-events-none">
                        <p class="text-amber-400 text-xs font-bold uppercase tracking-widest">{{ tx.contactMapLabel }}</p>
                        <p class="text-white text-sm font-semibold mt-0.5">{{ tx.contactMapName }}</p>
                    </div>
                    <iframe title="Sunset RentCar — San Pedro Sula, Honduras"
                        class="w-full h-full min-h-[420px]"
                        style="border:0; filter: invert(90%) hue-rotate(180deg) brightness(0.85) contrast(1.1) saturate(0.7);"
                        loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q=Barrio+Paz+Barahona,+Avenida+Circunvalacion,+San+Pedro+Sula,+Cortes,+Honduras&z=15&output=embed">
                    </iframe>
                    <a href="https://maps.google.com/?q=Barrio+Paz+Barahona,+Avenida+Circunvalacion,+San+Pedro+Sula,+Honduras"
                        target="_blank" rel="noopener noreferrer"
                        class="absolute bottom-4 right-4 z-10 flex items-center gap-2 bg-black/80 backdrop-blur-sm border border-amber-400/35 text-amber-400 hover:bg-amber-400 hover:text-black text-xs font-bold px-3 py-2 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        {{ tx.contactOpenMaps }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ GALERÍA ═══════════════════════════════════════════ -->
    <section class="overflow-hidden" style="background:#080604;">
        <div class="max-w-7xl mx-auto px-6 py-24">
            <div class="text-center mb-14" data-aos="fade-up">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-[0.3em]">{{ tx.galleryBadge }}</span>
                <h2 class="text-4xl sm:text-5xl font-black text-white mt-3">{{ tx.galleryTitle }}</h2>
                <p class="text-amber-100/40 mt-4 max-w-lg mx-auto">{{ tx.gallerySubtitle }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" data-aos="fade-up" data-aos-delay="100">
                <div class="md:col-span-2 md:row-span-2 relative group overflow-hidden rounded-2xl h-72 md:h-auto" style="min-height:400px;">
                    <img src="/img/pexels-mikebirdy-4003121.jpg" alt="Vehículo Sunset RentCar"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                        <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">{{ tx.galleryLabel1 }}</span>
                        <h3 class="text-white font-black text-2xl mt-1">{{ tx.galleryText1 }}</h3>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl h-56">
                    <img src="/img/pexels-vyacheslav-bobin-105199946-13033349.jpg" alt="Carretera Honduras"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">{{ tx.galleryLabel2 }}</span>
                        <h3 class="text-white font-bold text-base mt-0.5">{{ tx.galleryText2 }}</h3>
                    </div>
                </div>
                <div class="relative group overflow-hidden rounded-2xl h-56">
                    <img src="/img/pexels-alfonso-romo-2155321859-35153444.jpg" alt="Viaje en familia"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-4 left-4">
                        <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">{{ tx.galleryLabel3 }}</span>
                        <h3 class="text-white font-bold text-base mt-0.5">{{ tx.galleryText3 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════ FOOTER ════════════════════════════════════════════ -->
    <footer class="border-t pt-14 pb-8" style="background:#07060400; border-color: rgba(251,191,36,0.08); background:#070604;">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                <!-- Logo + descripción -->
                <div class="lg:col-span-2">
                    <img src="/img/logo.png" alt="Sunset RentCar" class="h-16 w-auto mb-4"
                        style="filter: drop-shadow(0 0 12px rgba(251,191,36,0.5)) brightness(1.15)"/>
                    <p class="text-amber-100/35 text-sm leading-relaxed max-w-xs">{{ tx.footerDesc }}</p>
                    <div class="flex items-center gap-3 mt-5">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-lg border border-amber-400/15 hover:border-amber-400/40 flex items-center justify-center text-amber-100/30 hover:text-amber-300 transition-all hover:bg-amber-400/5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-lg border border-amber-400/15 hover:border-amber-400/40 flex items-center justify-center text-amber-100/30 hover:text-amber-300 transition-all hover:bg-amber-400/5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a :href="getWhatsappLink()" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-lg border border-green-500/20 hover:border-green-500/40 flex items-center justify-center text-amber-100/30 hover:text-green-400 transition-all hover:bg-green-500/5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Navegación -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-widest">{{ tx.footerNav }}</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="#inicio"         class="text-amber-100/35 hover:text-amber-300 transition-colors">Inicio / Home</a></li>
                        <li><a href="#como-funciona"  class="text-amber-100/35 hover:text-amber-300 transition-colors">{{ tx.navHowItWorks }}</a></li>
                        <li><a href="#flota"          class="text-amber-100/35 hover:text-amber-300 transition-colors">{{ tx.navFleet }}</a></li>
                        <li><a href="#servicios"      class="text-amber-100/35 hover:text-amber-300 transition-colors">{{ tx.navServices }}</a></li>
                        <li><a href="#contacto"       class="text-amber-100/35 hover:text-amber-300 transition-colors">{{ tx.navContact }}</a></li>
                        <li><Link :href="route('login')" class="text-amber-100/35 hover:text-amber-300 transition-colors">{{ tx.navAdmin }}</Link></li>
                    </ul>
                </div>

                <!-- Contacto rápido -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-4 uppercase tracking-widest">{{ tx.footerContact }}</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2 text-amber-100/35"><span class="text-amber-400 mt-0.5">📍</span><span>Barrio Paz Barahona, San Pedro Sula</span></li>
                        <li class="flex items-center gap-2 text-amber-100/35"><span class="text-green-400">📱</span><a href="tel:+50432480912" class="hover:text-amber-300 transition-colors">+504 3248-0912</a></li>
                        <li class="flex items-center gap-2 text-amber-100/35"><span class="text-amber-400">✉️</span><a href="mailto:info@sunsetrentcar.com" class="hover:text-amber-300 transition-colors">info@sunsetrentcar.com</a></li>
                        <li class="flex items-center gap-2 text-amber-100/35"><span class="text-amber-400">🕐</span><span>{{ tx.footerScheduleRow }}</span></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom -->
            <div class="border-t pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs"
                 style="border-color: rgba(251,191,36,0.08); color: rgba(251,191,36,0.2);">
                <span>© {{ new Date().getFullYear() }} Sunset RentCar. {{ tx.footerRights }}</span>
                <span>{{ tx.footerDev }} <span style="color:rgba(251,191,36,0.45)">JCVR Digital Solutions</span></span>
            </div>
        </div>
    </footer>

    <!-- Botón WhatsApp flotante -->
    <a :href="getWhatsappLink()" target="_blank"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-400 text-white rounded-full
               shadow-2xl shadow-green-900/50 flex items-center justify-center
               transition-all hover:scale-110 hover:rotate-6"
        title="WhatsApp">
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.117.553 4.103 1.523 5.83L.057 23.886a.5.5 0 00.608.61l6.233-1.638A11.944 11.944 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.89 0-3.663-.522-5.179-1.43l-.371-.219-3.845 1.01 1.029-3.74-.24-.385A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
    </a>
</template>

<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1) opacity(0.35);
    cursor: pointer;
}
input[type="date"]::-webkit-calendar-picker-indicator:hover {
    filter: invert(1) opacity(0.75);
}

/* ── 3D Hero Title ───────────────────────────────────────────── */
.hero-title-3d {
    text-shadow:
        0 2px 0 rgba(0,0,0,0.5),
        0 4px 12px rgba(0,0,0,0.4),
        0 0 60px rgba(251,146,60,0.1);
    transform-style: preserve-3d;
}

/* ── Shimmer text ────────────────────────────────────────────── */
.shimmer-text {
    background: linear-gradient(
        90deg,
        #f59e0b 0%,
        #fcd34d 20%,
        #fb923c 40%,
        #fde68a 55%,
        #f97316 70%,
        #fbbf24 85%,
        #f59e0b 100%
    );
    background-size: 250% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmer 4s linear infinite;
    display: block;
}
@keyframes shimmer {
    0%   { background-position: 0%   center; }
    100% { background-position: 250% center; }
}

/* ── Badge float ─────────────────────────────────────────────── */
.badge-float {
    animation: badgeFloat 3s ease-in-out infinite;
}
@keyframes badgeFloat {
    0%, 100% { transform: translateY(0px); }
    50%       { transform: translateY(-5px); }
}

/* ── Glow rings ──────────────────────────────────────────────── */
.glow-ring {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width:  var(--s);
    height: var(--s);
    border-radius: 50%;
    background: radial-gradient(circle, var(--c) 0%, transparent 70%);
    animation: ringPulse 5s ease-in-out infinite;
    animation-delay: var(--d);
}
@keyframes ringPulse {
    0%, 100% { transform: translateX(-50%) scale(1);    opacity: 1; }
    50%       { transform: translateX(-50%) scale(1.12); opacity: 0.6; }
}

/* ── Stats bar shine ─────────────────────────────────────────── */
.stats-shine {
    background: linear-gradient(
        105deg,
        transparent 30%,
        rgba(255,255,255,0.18) 50%,
        transparent 70%
    );
    background-size: 300% 100%;
    animation: statsShine 3.5s linear infinite;
}
@keyframes statsShine {
    0%   { background-position: 200% center; }
    100% { background-position: -100% center; }
}

/* ── Stat card 3D hover ──────────────────────────────────────── */
.stat-card-3d {
    transition: transform 0.3s ease;
}
.stat-card-3d:hover {
    transform: translateY(-6px) scale(1.05);
}

/* ── Tilt card transition ────────────────────────────────────── */
.tilt-card {
    transform-style: preserve-3d;
    will-change: transform;
}
.tilt-card img {
    transition: transform 0.15s ease !important;
    will-change: transform;
}
</style>
