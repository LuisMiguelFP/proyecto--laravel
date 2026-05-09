<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const particles = ref([])
const scanlines = ref([])
const gridLines = ref([])

onMounted(() => {
    // Partículas flotantes
    particles.value = Array.from({ length: 30 }, (_, i) => ({
        id: 'p' + i,
        left: Math.random() * 100,
        top: Math.random() * 100,
        size: 2 + Math.random() * 4,
        duration: 15 + Math.random() * 25,
        delay: -(Math.random() * 20),
        opacity: 0.3 + Math.random() * 0.7,
    }))

    // Líneas de escaneo
    scanlines.value = Array.from({ length: 3 }, (_, i) => ({
        id: 's' + i,
        top: Math.random() * 100,
        duration: 8 + Math.random() * 12,
        delay: -(Math.random() * 10),
        opacity: 0.1 + Math.random() * 0.3,
    }))

    // Cuadrícula de fondo
    gridLines.value = Array.from({ length: 20 }, (_, i) => ({
        id: 'g' + i,
        position: Math.random() * 100,
        horizontal: Math.random() > 0.5,
        opacity: 0.03 + Math.random() * 0.08,
    }))
})
</script>

<template>
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <!-- Fondo base con gradiente -->
        <div class="absolute inset-0 bg-[#0B0E14]">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_30%_20%,rgba(34,197,94,0.15)_0%,transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_70%_80%,rgba(59,130,246,0.1)_0%,transparent_50%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_50%,rgba(168,85,247,0.05)_0%,transparent_70%)]"></div>
        </div>

        <!-- Cuadrícula de fondo -->
        <svg class="absolute inset-0 w-full h-full opacity-20">
            <defs>
                <pattern id="grid" width="60" height="60" patternUnits="userSpaceOnUse">
                    <path d="M 60 0 L 0 0 0 60" fill="none" stroke="rgba(34,197,94,0.1)" stroke-width="0.5"/>
                </pattern>
                <pattern id="grid-small" width="15" height="15" patternUnits="userSpaceOnUse">
                    <circle cx="7.5" cy="7.5" r="0.5" fill="rgba(34,197,94,0.3)"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)"/>
            <rect width="100%" height="100%" fill="url(#grid-small)"/>
        </svg>

        <!-- Líneas de escaneo -->
        <div
            v-for="s in scanlines"
            :key="s.id"
            class="absolute left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-neon-green-500/40 to-transparent animate-scanline"
            :style="{
                top: s.top + '%',
                animationDuration: s.duration + 's',
                animationDelay: s.delay + 's',
                opacity: s.opacity,
            }"
        ></div>

        <!-- Partículas de luz -->
        <div
            v-for="p in particles"
            :key="p.id"
            class="absolute rounded-full bg-neon-green-400 animate-pulse-slow"
            :style="{
                left: p.left + '%',
                top: p.top + '%',
                width: p.size + 'px',
                height: p.size + 'px',
                animationDuration: p.duration + 's',
                animationDelay: p.delay + 's',
                opacity: p.opacity,
                boxShadow: '0 0 ' + (p.size * 3) + 'px rgba(34,197,94,' + (p.opacity * 0.5) + ')',
            }"
        ></div>

        <!-- Efecto de viñeta -->
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_50%,rgba(0,0,0,0.4)_100%)] pointer-events-none"></div>

        <!-- Líneas de borde decorativas -->
        <div class="absolute top-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-gradient-to-r from-transparent via-neon-green-500/30 to-transparent"></div>
        <div class="absolute top-0 left-0 h-full w-[1px] bg-gradient-to-b from-transparent via-neon-green-500/30 to-transparent"></div>
        <div class="absolute top-0 right-0 h-full w-[1px] bg-gradient-to-b from-transparent via-neon-green-500/30 to-transparent"></div>
    </div>
</template>

<style>
@keyframes scanline {
    0% {
        transform: translateY(-100vh);
        opacity: 0;
    }
    10% {
        opacity: 0.3;
    }
    90% {
        opacity: 0.3;
    }
    100% {
        transform: translateY(100vh);
        opacity: 0;
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
        opacity: var(--tw-opacity, 0.5);
    }
    50% {
        transform: scale(1.5);
        opacity: calc(var(--tw-opacity, 0.5) * 0.7);
    }
}

.animate-scanline {
    animation: scanline linear infinite;
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}
</style>
