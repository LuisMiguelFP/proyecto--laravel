<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AnimatedBackground from '@/Components/AnimatedBackground.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Bienvenido" />

    <div class="relative min-h-screen bg-[#0B0E14] text-white selection:bg-neon-green-500 selection:text-black overflow-hidden font-sans">
        <AnimatedBackground />

        <!-- Navigation -->
        <nav class="relative z-10 border-b border-[#1E232D] bg-[#0B0E14]/80 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="size-10 bg-neon-green-500 rounded-xl flex items-center justify-center shadow-[0_0_15px_rgba(34,197,94,0.4)]">
                        <span class="text-[#0B0E14] text-2xl font-black tracking-tighter">S</span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Sala <span class="text-neon-green-500">Electrónica</span></span>
                </div>

                <div v-if="canLogin" class="flex gap-4 items-center font-semibold text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-5 py-2.5 rounded-xl bg-[#1E232D] hover:bg-[#2A303C] text-white transition-colors border border-[#2A303C]"
                    >
                        Mi Panel
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="text-gray-300 hover:text-white transition-colors"
                        >
                            Iniciar sesión
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-5 py-2.5 rounded-xl bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-bold transition-colors shadow-[0_0_15px_rgba(34,197,94,0.3)] hover:shadow-[0_0_20px_rgba(34,197,94,0.5)]"
                        >
                            Registrarse
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative z-10 flex flex-col items-center justify-center min-h-[calc(100vh-5rem)] px-6 text-center">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-neon-green-500/10 via-[#0B0E14] to-[#0B0E14] -z-10 pointer-events-none"></div>

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#1E232D] border border-neon-green-500/30 text-neon-green-400 text-sm font-semibold mb-8 shadow-[0_0_10px_rgba(34,197,94,0.1)]">
                <span class="relative flex size-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-neon-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full size-2 bg-neon-green-500"></span>
                </span>
                Reserva tu espacio 24/7
            </div>

            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6 max-w-4xl text-white">
                Tu laboratorio de electrónica, <span class="text-transparent bg-clip-text bg-gradient-to-r from-neon-green-500 to-emerald-400 drop-shadow-[0_0_15px_rgba(34,197,94,0.5)]">a un clic.</span>
            </h1>

            <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl">
                Accede a equipamiento profesional, herramientas de medición y espacios adaptados para todos tus proyectos de hardware e IoT.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <Link
                    href="/spaces"
                    class="px-8 py-4 rounded-xl bg-neon-green-500 hover:bg-neon-green-400 text-[#0B0E14] font-black text-lg transition-all shadow-[0_0_20px_rgba(34,197,94,0.4)] hover:shadow-[0_0_30px_rgba(34,197,94,0.6)] hover:-translate-y-1"
                >
                    Ver Espacios
                </Link>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="route('register')"
                    class="px-8 py-4 rounded-xl bg-[#1E232D] hover:bg-[#2A303C] text-white border border-[#2A303C] font-bold text-lg transition-all hover:-translate-y-1"
                >
                    Crear Cuenta
                </Link>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl w-full mt-24 text-left">
                <div class="bg-[#0F1219] p-6 rounded-2xl border border-[#1E232D] shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                    <div class="size-12 bg-neon-green-500/10 text-neon-green-500 rounded-xl flex items-center justify-center mb-4 border border-neon-green-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Equipamiento Pro</h3>
                    <p class="text-gray-400 text-sm">Desde multímetros hasta osciloscopios y estaciones de soldado SMD, todo lo que necesitas.</p>
                </div>
                <div class="bg-[#0F1219] p-6 rounded-2xl border border-[#1E232D] shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                    <div class="size-12 bg-neon-green-500/10 text-neon-green-500 rounded-xl flex items-center justify-center mb-4 border border-neon-green-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Reserva Flexible</h3>
                    <p class="text-gray-400 text-sm">Elige tu horario y el nivel de herramientas que necesitas, pagando solo por el tiempo de uso.</p>
                </div>
                <div class="bg-[#0F1219] p-6 rounded-2xl border border-[#1E232D] shadow-[0_10px_30px_rgba(0,0,0,0.5)]">
                    <div class="size-12 bg-neon-green-500/10 text-neon-green-500 rounded-xl flex items-center justify-center mb-4 border border-neon-green-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Entorno Ideal</h3>
                    <p class="text-gray-400 text-sm">Espacios limpios, con excelente iluminación y preparados para evitar descargas electrostáticas.</p>
                </div>
            </div>
        </main>
    </div>
</template>
