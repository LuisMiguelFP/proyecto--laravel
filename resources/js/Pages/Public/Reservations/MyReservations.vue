<script setup>
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    reservations: Array,
})

const statusColors = {
    pendiente:  'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    confirmada: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    rechazada:  'bg-red-500/20 text-red-400 border border-red-500/30',
    cancelada:  'bg-gray-500/20 text-gray-400 border border-gray-500/30',
    finalizada: 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
}

const levelColors = {
    basica:   'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    estandar: 'bg-neon-green-500/20 text-neon-green-400 border border-neon-green-500/30',
    gold:     'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
}

function formatPrice(price) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(price)
}
</script>

<template>
    <div class="min-h-screen bg-[#0B0E14]">
        <header class="bg-[#0F1219] shadow-sm border-b border-[#1E232D]">
            <div class="max-w-4xl mx-auto px-4 py-5 flex items-center justify-between">
                <h1 class="text-xl font-bold text-white">Mis reservas</h1>
                <a href="/" class="text-sm font-semibold text-neon-green-500 hover:text-neon-green-400 transition-colors">Volver al inicio</a>
            </div>
        </header>

        <main class="max-w-4xl mx-auto px-4 py-8">
            <div v-if="reservations.length" class="space-y-4">
                <div
                    v-for="r in reservations"
                    :key="r.slug"
                    class="bg-[#0F1219] rounded-xl border border-[#1E232D] shadow-[0_4px_25px_rgba(0,0,0,0.5)] p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                >
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-white">{{ r.space_name }}</span>
                            <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', levelColors[r.level]]">
                                {{ r.level_label }}
                            </span>
                            <span :class="['text-xs px-2 py-0.5 rounded-full font-medium', statusColors[r.status]]">
                                {{ r.status }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400">{{ r.start_time }} — {{ r.end_time }}</p>
                        <p v-if="r.notes" class="text-xs text-gray-500">{{ r.notes }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-neon-green-500">{{ formatPrice(r.total_price) }}</p>
                        <p class="text-xs text-gray-500">total</p>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-20 text-gray-500">
                <p class="text-lg font-medium">No tienes reservas aún</p>
                <a href="/" class="mt-3 inline-block text-sm font-semibold text-neon-green-500 hover:text-neon-green-400 transition-colors">Reservar ahora</a>
            </div>
        </main>
    </div>
</template>